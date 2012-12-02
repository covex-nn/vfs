<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Entity/Abstract.php";

require_once "JooS/Stream/Entity/Virtual/Interface.php";

/**
 * Virtual stream entity
 */
class JooS_Stream_Entity_Virtual  extends JooS_Stream_Entity_Abstract
  implements JooS_Stream_Entity_Virtual_Interface
{
  
  /**
   * @var JooS_Stream_Entity_Interface
   */
  private $_realEntity;
  
  /**
   * Create new virtual stream entity
   * 
   * @param JooS_Stream_Entity_Interface $realEntity Real stream entity
   * @param string                       $path       Tmp path
   * 
   * @return JooS_Stream_Entity_Virtual
   */
  public static function newInstance(JooS_Stream_Entity_Interface $realEntity, $path) {
    
    $basename = $realEntity->basename();
    $instance = new self($basename, $path);
    /* @var $instance JooS_Stream_Entity_Virtual */
    $instance->_setRealEntity($realEntity);
    
    return $instance;
  }
  
  /**
   * Store old entity
   * 
   * @param JooS_Stream_Entity_Interface $realEntity
   * 
   * @return null
   */
  private function _setRealEntity(JooS_Stream_Entity_Interface $realEntity) {
    $this->_realEntity = $realEntity;
  }
  
  /**
   * Return saved old entity
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function getRealEntity() {
    return $this->_realEntity;
  }
  
}
