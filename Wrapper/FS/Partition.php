<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Wrapper/FS/Partition/Interface.php";

/**
 * Filesystem tree.
 * 
 * @todo все функции должны возвращать вместо Storage Entity !!!
 */
class JooS_Stream_Wrapper_FS_Partition implements JooS_Stream_Wrapper_FS_Partition_Interface
{
  
  /**
   * @var JooS_Stream_Entity
   */
  private $_root = null;
  
  /**
   * @var JooS_Stream_Wrapper_FS_Partition_Changes_Linear
   */
  private $_changesLinear = null;
  
  /**
   * @var JooS_Stream_Wrapper_FS_Partition_Changes_Tree
   */
  private $_changesTree = null;
  
  /**
   * Constructor
   * 
   * @param JooS_Stream_Entity_Interface $content Folder
   */
  public function __construct(JooS_Stream_Entity_Interface $content)
  {
    $this->setRoot($content);

    require_once "JooS/Stream/Wrapper/FS/Partition/Changes/Linear.php";
    
    $this->_changesLinear = new JooS_Stream_Wrapper_FS_Partition_Changes_Linear();

    require_once "JooS/Stream/Wrapper/FS/Partition/Changes/Tree.php";
    
    $this->_changesTree = new JooS_Stream_Wrapper_FS_Partition_Changes_Tree();
  }
  
  /**
   * @see http://www.refreshinglyblue.com/2008/11/26/recursively-delete-a-non-empty-directory-with-php5/
   */
  public function __destruct()
  {
    $dir = $this->_getSystemTempDirectory(false);
    if (!is_null($dir)) {
      $rdIterator = new RecursiveDirectoryIterator($dir);
      $riIterator = new RecursiveIteratorIterator(
        $rdIterator, RecursiveIteratorIterator::CHILD_FIRST
      );
      foreach ($riIterator as $file) {
        if ($file->isDir()) {
          rmdir($file->getPathname());
        } else {
          unlink($file->getPathname());
        }
      }
      rmdir($dir);
    }
  }
  
  /**
   * Return root of filesystem
   * 
   * @return JooS_Stream_Entity
   */
  public function getRoot() {
    return $this->_root;
  }

  /**
   * Init root
   * 
   * @param JooS_Stream_Entity_Interface $entity Folder
   * 
   * @return null
   * @throws JooS_Stream_Wrapper_FS_Exception
   */
  protected function setRoot(JooS_Stream_Entity_Interface $entity) {
    if (!$entity->file_exists() || !$entity->is_dir())
    {
      require_once "JooS/Stream/Wrapper/FS/Exception.php";
      
      throw new JooS_Stream_Wrapper_FS_Exception(
        "Root folder is not valid"
      );
    }
    
    $this->_root = $entity;
  }
  
  /**
   * Return file/directory entity
   * 
   * @param string $filename Path to file/directory
   * 
   * @return JooS_Stream_Entity_Interface
   * @throws JooS_Stream_Wrapper_FS_Exception
   */
  public function getEntity($filename) {
    $unixFilename = str_replace("\\", "/", $filename);
    $parts = explode(
      "/", trim($unixFilename, "/")
    );
    $basename = array_pop($parts);
    
    $filepath = "";
    $partiallyFilepath = "";
    $changesStage = false;
    $directory = $this->getRoot();

    foreach ($parts as $name) {
      $filepath .= ($filepath ? "/" : "") . $name;
      $partiallyFilepath = ($partiallyFilepath ? "/" : "") . $name;
      
      $changesExists = $this->_changesLinear->exists($filepath);
      if ($changesExists || $changesStage) {
        $changesStage = true;
        if ($changesExists) {
          $directory = $this->_changesLinear->get($filepath);
          $partiallyFilepath = "";
          if (!$directory->file_exists() || !$directory->is_dir()) {
            return null;
          }
        } else {
          return null;
        }
      } else {
        $path = $directory->path() . "/" . $filepath;
        if (!file_exists($path) || !is_dir($path)) {
          return null;
        }
      }
    }
    
    if ($this->_changesLinear->exists($filename)) {
      $entity = $this->_changesLinear->get($filename);
    } else {
      require_once "JooS/Stream/Entity.php";

      $entity = JooS_Stream_Entity::newInstance(
        $directory->path() .
        ($partiallyFilepath ? "/" . $partiallyFilepath : "") .
        "/" . $basename
      );
    }
    return $entity;
  }
  
  /**
   * Create a directory
   *
   * @param string $path    Path
   * @param int    $mode    Mode
   * @param int    $options Options
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function makeDirectory($path, $mode, $options) {
    $result = null;
    
    $entity = $this->getEntity($path);
    if (!is_null($entity)) {
      if (!$entity->file_exists()) {
        $tmpPath = $this->_makeDirectory($mode);
        
        require_once "JooS/Stream/Entity/Virtual.php";
        
        $result = JooS_Stream_Entity_Virtual::newInstance($entity, $tmpPath);
        $this->_changesRegister($path, $result);
      }
    } else {
      /* @todo сделать поддержку STREAM_MKDIR_RECURSIVE */
    }
    
    if (is_null($result) && ($options & STREAM_REPORT_ERRORS)) {
      trigger_error(
        "Could not create directory '$path'", E_WARNING
      );
    }
    
    return $result;
  }
  
  /**
   * Return list of file in path
   * 
   * @param string $path Path
   * 
   * @return array
   */
  public function getList($path) {
    $entity = $this->getEntity($path);
    
    if (!is_null($entity) && $entity->file_exists() && $entity->is_dir()) {
      $files = array();
      $changes = $this->_changesTree->own($path);

      if (!($entity instanceof JooS_Stream_Entity_Virtual_Interface)) {
        $directory = $this->getRoot();
        $directoryPath = $directory->path() . "/" . $path;
        
        $dh = opendir($directoryPath);
        if ($dh) {
          require_once "JooS/Stream/Entity.php";
          
          while (true) {
            $file = readdir($dh);
            if ($file === false) {
              break;
            } elseif ($file == "." || $file == "..") {
              continue;
            }
            
            $changesKey = $path . "/" . $file;
            if (isset($changes[$changesKey])) {
              continue;
            } else {
              $files[$changesKey] = JooS_Stream_Entity::newInstance($changesKey);
            }
          }
          closedir($dh);
        }
      }
      
      foreach ($changes as $changesKey => $file) {
        /* @var $file JooS_Stream_Entity_Interface */
        if ($file instanceof JooS_Stream_Entity_Deleted_Interface) {
          unset($changes[$changesKey]);
        }
      }
      
      $mergedFiles = array_merge($files, $changes);
      ksort($mergedFiles);
      
      $result = array_values($mergedFiles);
    } else {
      $result = null;
    }
    
    return $result;
  }
  
  public function removeDirectory($path, $options) {
// STREAM_MKDIR_RECURSIVE
// if ($options & STREAM_REPORT_ERRORS) {
  }

  /**
   * Register changes in FS
   * 
   * @param string                       $path   Path to changes
   * @param JooS_Stream_Entity_Interface $entity Entity
   * 
   * @return null
   */
  protected function _changesRegister($path, JooS_Stream_Entity_Interface $entity) {
    $this->_changesLinear->add($path, $entity);
    $this->_changesTree->add($path, $entity);
  }
  
  /**
   * Creates directory in sys_get_temp_dir()
   * 
   * @return string
   */
  protected function _makeDirectory($mode) {
    $name = $this->_getUniqueFilename();
    mkdir($name, $mode);
    
    return $name;
  }

  private $_uniqueFilenameCounter = 0;
  
  /**
   * Return unique filename
   *
   * @return string
   */
  protected function _getUniqueFilename() {
    $sysTempDir = $this->_getSystemTempDirectory();
    do {
      $this->_uniqueFilenameCounter++;
      $name = $sysTempDir . "/" . $this->_uniqueFilenameCounter;
    } while (file_exists($name));
    
    return $name;
  }
  
  /**
   * @var string
   */
  private $_systemTempDirectory = null;
  
  /**
   * Return path to own temp directory
   * 
   * @param boolean $create Create new folder ?
   * 
   * @return string
   */
  protected function _getSystemTempDirectory($create = true) {
    if (is_null($this->_systemTempDirectory) && $create) {
      $sysTmpDir = rtrim(sys_get_temp_dir(), "\\/");
      /**
       * @todo надо наверное как-то ограничить количество итераций
       * @todo а с другой стороны - это не протестируешь =(
       */
      do {
        $name = $sysTmpDir . "/" . uniqid("fs", true);
      } while (file_exists($name));

      mkdir($name, 0777);
      $this->_systemTempDirectory = $name;
    }
    return $this->_systemTempDirectory;
  }
    
}
