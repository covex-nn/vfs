<?php

/**
 * @package JooS
 * @subpackage Stream
 */
namespace JooS\Stream;

/**
 * Deleted stream entity
 */
class Entity_Deleted extends Entity_Abstract implements Entity_Deleted_Interface
{
  
  /**
   * @var Entity_Interface
   */
  protected $_realEntity;
  
  /**
   * Create new virtual stream entity
   * 
   * @param Entity_Interface $realEntity Real stream entity
   * 
   * @return Entity_Deleted
   */
  public static function newInstance(Entity_Interface $realEntity)
  {
    $basename = $realEntity->basename();
    $path = $realEntity->path();
    
    $instance = new static($basename, $path);
    /* @var $instance Entity_Deleted */
    $instance->_realEntity = $realEntity;
    
    return $instance;
  }
  
  /**
   * Return saved old entity
   * 
   * @return Entity_Interface
   */
  public function getRealEntity()
  {
    return $this->_realEntity;
  }
  
  /**
   * File exists ?
   * 
   * @return booleans
   */
  public function file_exists()
  {
    return false;
  }
}
