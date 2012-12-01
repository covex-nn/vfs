<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Wrapper/FS/Partition/Interface.php";

/**
 * Filesystem tree.
 */
class JooS_Stream_Wrapper_FS_Partition implements JooS_Stream_Wrapper_FS_Partition_Interface
{
  
  /**
   * @var JooS_Stream_Storage
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

    require_once "JooS/Stream/Wrapper/FS/Partition/Changes/.php";
    
    $this->_changesTree = new JooS_Stream_Wrapper_FS_Partition_Changes_Tree();
  }
  
  /**
   * Create a directory
   *
   * @param string $path    Path
   * @param int    $mode    Mode
   * @param int    $options Options
   * 
   * @return boolean
   */
  public function makeDirectory($path, $mode, $options) {
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
    $directory = $this->getRoot()->entity();
    
    foreach ($parts as $name) {
      $partiallyPath .= "/" . $name;
      $path = $directory->path() . $partiallyPath;
      
      if (false) {
        /** @todo сделать проверку на изменения в текущей fs */
        
        // $partiallyPath = "";
        // $directory = ...
      }
      elseif (!file_exists($path) || !is_dir($path)) {
        return null;
      }
    }
    
    $path = $directory->path() . $partiallyPath . "/" . $basename;
    
    /** @todo надо сделать проверку на изменения в текущей fs */
    
    require_once "JooS/Stream/Entity.php";
    
    return JooS_Stream_Entity::newInstance($path);
  }
  
  /**
   * Return root of filesystem
   * 
   * @return JooS_Stream_Storage
   */
  public function getRoot() {
    return $this->_root;
  }
  
  /**
   * Init root
   * 
   * @param JooS_Stream_Entity_Interface $content Folder
   * 
   * @return null
   * @throws JooS_Stream_Wrapper_FS_Exception
   */
  protected function setRoot(JooS_Stream_Entity_Interface $content) {
    if (!$content->file_exists() || !$content->is_dir())
    {
      require_once "JooS/Stream/Wrapper/FS/Exception.php";
      
      throw new JooS_Stream_Wrapper_FS_Exception(
        "Root folder is not valid"
      );
    }
    
    require_once "JooS/Stream/Storage.php";
    
    $this->_root = new JooS_Stream_Storage($content);
  }
  
}
