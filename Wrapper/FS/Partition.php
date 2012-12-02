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
    
    $partiallyPath = "";
    $directory = $this->getRoot();
    
    foreach ($parts as $name) {
      $partiallyPath .= "/" . $name;
      $path = $directory->path() . $partiallyPath;
      
      if (false) {
        /** @todo сделать проверку на изменения в текущей fs */
        
        // $partiallyPath = "";
        // $directory = ...
      } elseif (!file_exists($path) || !is_dir($path)) {
        return null;
      }
    }
    
    $path = $directory->path() . $partiallyPath . "/" . $basename;
    
    /** @todo надо сделать проверку на изменения в текущей fs */
    
    require_once "JooS/Stream/Entity.php";
    
    return JooS_Stream_Entity::newInstance($path);
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
