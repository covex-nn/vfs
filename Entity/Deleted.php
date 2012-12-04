<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Entity/Virtual.php";

require_once "JooS/Stream/Entity/Deleted/Interface.php";

/**
 * Deleted stream entity
 */
class JooS_Stream_Entity_Deleted extends JooS_Stream_Entity_Abstract
  implements JooS_Stream_Entity_Deleted_Interface
{
  
  /**
   * @var JooS_Stream_Entity_Interface
   */
  private $_realEntity;
  
  /**
   * Create new virtual stream entity
   * 
   * @param JooS_Stream_Entity_Interface $realEntity Real stream entity
   * 
   * @return JooS_Stream_Entity_Deleted
   */
  public static function newInstance(JooS_Stream_Entity_Interface $realEntity) {
    $basename = $realEntity->basename();
    $path = $realEntity->path();
    
    $instance = new static($basename, $path);
    /* @var $instance JooS_Stream_Entity_Deleted */
    $instance->_realEntity = $realEntity;
    
    return $instance;
  }
  
  /**
   * Return saved old entity
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function getRealEntity() {
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
