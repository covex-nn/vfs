<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Wrapper.php";

/**
 * Stream for local file system
 */
class JooS_Stream_Wrapper_FS extends JooS_Stream_Wrapper
{
  
  /**
   * @var array
   */
  protected static $instances = array();
  
  /**
   * Set up filesystem before use
   * 
   * @param string $name Name of FS
   * @param string $root Root of FS
   * 
   * @return null
   * @throws JooS_Stream_Wrapper_FS_Exception
   */
  public static function fsSetup($name, $root) {
    if (self::fsExists($name)) {
      require_once "JooS/Stream/Wrapper/FS/Exception.php";
      
      throw new JooS_Stream_Wrapper_FS_Exception(
        "Filesystem '$name' has been already initialized"
      );
    }

    require_once "JooS/Stream/Entity.php";
    
    $content = JooS_Stream_Entity::newInstance($root);
    
    require_once "JooS/Stream/Wrapper/FS/Tree.php";
    
    self::$instances[$name] = new JooS_Stream_Wrapper_FS_Tree($content);
  }
  
  /**
   * Delete filesystem information
   * 
   * @param string $name Name of FS
   * 
   * @return null
   */
  public static function fsClear($name) {
    if (self::fsExists($name)) {
      unset(self::$instances[$name]);
    }
  }
  
  /**
   * Is filesystem exists ?
   * 
   * @param string $name Name of FS
   * 
   * @return boolean
   */
  public static function fsExists($name) {
    return isset(self::$instances[$name]);
  }
  
}
