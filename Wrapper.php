<?php

/**
 * @package JooS
 */

/**
 * Stream wrapper abstract class.
 */
abstract class JooS_Stream_Wrapper
{
  
  /**
   * @var string
   */
  protected static $_protocol;
  
  /**
   * Register stream wrapper
   * 
   * @param string $protocol Protocol name
   * @param int $flags Should be set to STREAM_IS_URL if protocol is a URL protocol
   * 
   * @throws JooS_Stream_Wrapper_Exception
   * @return boolean
   */
  public static function register($protocol, $flags = 0)
  {
    $wrappers = stream_get_wrappers();
    if (in_array($protocol, $wrappers)) {
      require_once "JooS/Stream/Wrapper/Exception.php";
      
      throw new JooS_Stream_Wrapper_Exception(
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
   * @throws JooS_Stream_Wrapper_Exception
   * @return boolean
   */
  public static function unregister($protocol)
  {
    $wrappers = stream_get_wrappers();
    if (!in_array($protocol, $wrappers)) {
      require_once "JooS/Stream/Wrapper/Exception.php";
      
      throw new JooS_Stream_Wrapper_Exception(
        "Protocol '$protocol' has not been registered yet"
      );
    }
    
    return stream_wrapper_unregister($protocol);
  }
  
}

