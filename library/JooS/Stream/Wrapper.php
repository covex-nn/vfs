<?php

/**
 * @package JooS
 * @subpackage Stream
 */
namespace JooS\Stream;

/**
 * Stream wrapper abstract class.
 */
abstract class Wrapper
{
  
  /**
   * @var string
   */
  protected static $_protocol;
  
  /**
   * Register stream wrapper
   * 
   * @param string $protocol Protocol name
   * @param int    $flags    STREAM_IS_URL if protocol is a URL protocol, or 0
   * 
   * @throws Wrapper_Exception
   * @return boolean
   */
  public static function register($protocol, $flags = 0)
  {
    $wrappers = stream_get_wrappers();
    if (in_array($protocol, $wrappers)) {
      require_once "JooS/Stream/Wrapper/Exception.php";
      
      throw new Wrapper_Exception(
        "Protocol '$protocol' has been already registered"
      );
    }
    $className = get_called_class();
    
    return stream_wrapper_register($protocol, $className, $flags);
  }
  
  /**
   * Unregister stream wrapper
   * 
   * @param string $protocol Protocol name
   * 
   * @throws Wrapper_Exception
   * @return boolean
   */
  public static function unregister($protocol)
  {
    $wrappers = stream_get_wrappers();
    if (!in_array($protocol, $wrappers)) {
      require_once "JooS/Stream/Wrapper/Exception.php";
      
      throw new Wrapper_Exception(
        "Protocol '$protocol' has not been registered yet"
      );
    }
    
    return stream_wrapper_unregister($protocol);
  }
  
}

