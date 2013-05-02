<?php

/**
 * @package JooS
 * @subpackage Stream
 */
namespace JooS\Stream;

/**
 * Virtual stream entity
 */
class Entity_Virtual  extends Entity_Abstract implements Entity_Virtual_Interface
{
  
  /**
   * @var Entity_Interface
   */
  protected $_realEntity;
  
  /**
   * Create new virtual stream entity
   * 
   * @param Entity_Interface $entity Real stream entity
   * @param string           $path   Tmp path
   * @param string           $name   Optional basename
   * 
   * @return Entity_Virtual
   */
  public static function newInstance(Entity_Interface $entity, $path, $name = null)
  {
    if (is_null($name)) {
      $name = $entity->basename();
    }
    $instance = new static($name, $path);
    /* @var $instance JooS_Stream_Entity_Virtual */
    $instance->_realEntity = $entity;
    
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
  
}
