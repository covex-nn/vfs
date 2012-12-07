<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Wrapper/FS/Partition/Changes/Interface.php";

/**
 * Partition changes, linear data.
 */
class JooS_Stream_Wrapper_FS_Partition_Changes_Linear
  implements JooS_Stream_Wrapper_FS_Partition_Changes_Interface
{
  
  /**
   * @var array
   */
  private $_data;
  
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->_data = array();
  }
  
  /**
   * Return stream storage
   * 
   * @param string $path Path
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function get($path)
  {
    $storage = null;
    if ($this->exists($path)) {
      $storage = $this->_data[$path];
    }
    return $storage;
  }
  
  /**
   * Add stream storage to changes array
   * 
   * @param string                       $path   Path
   * @param JooS_Stream_Entity_Interface $entity Stream entity
   * 
   * @return boolean
   */
  public function add($path, JooS_Stream_Entity_Interface $entity)
  {
    $this->_data[$path] = $entity;

    return true;
  }
  
  /**
   * Delete stream storage from array
   * 
   * @param string $path Path
   * 
   * @return boolean
   */
  public function delete($path)
  {
    $result = false;
    if ($this->exists($path)) {
      unset($this->_data[$path]);
      $result = true;
    }
    return $result;
  }
  
  /**
   * Is $path added to array ?
   * 
   * @param string $path Path
   * 
   * @return boolean
   */
  public function exists($path)
  {
    return isset($this->_data[$path]);
  }
  
  /**
   * Count elements
   * 
   * @return int
   */
  public function count()
  {
    return sizeof($this->_data);
  }
}
