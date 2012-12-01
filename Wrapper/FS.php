<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Wrapper.php";

require_once "JooS/Stream/Wrapper/FS/Interface.php";
/**
 * Stream for local file system
 */
class JooS_Stream_Wrapper_FS extends JooS_Stream_Wrapper implements JooS_Stream_Wrapper_FS_Interface
{

  /**
   * Retrieve information about a file
   *
   * @param string $url   Url
   * @param int    $flags Flags
   * 
   * @return array
   */
  public function url_stat($url, $flags) {
    $path = self::getPath($url);
    
    if (is_null($path)) {
      $stat = false;
    } elseif ($flags & STREAM_URL_STAT_QUIET) {
      $stat = @stat($path);
    } else {
      $stat = stat($path);
    }

    return $stat;
  }

  /**
   * @var array
   */
  protected static $partitions = array();
  
  /**
   * Register stream wrapper
   * 
   * @param string $protocol Protocol name
   * @param string $root     FS root directory
   * 
   * @return boolean
   */
  public static function register($protocol, $root)
  {
    $result = parent::register($protocol, 0);
    if ($result) {
      require_once "JooS/Stream/Entity.php";

      $content = JooS_Stream_Entity::newInstance($root);

      require_once "JooS/Stream/Wrapper/FS/Partition.php";

      self::$partitions[$protocol] = new JooS_Stream_Wrapper_FS_Partition($content);
    }
    return $result;
  }
  
  /**
   * Unregister stream wrapper
   * 
   * @param string $protocol Protocol name
   * 
   * @return boolean
   */
  public static function unregister($protocol)
  {
    unset(self::$partitions[$protocol]);
    
    return parent::unregister($protocol);
  }
  
  /**
   * Return partition by file url
   * 
   * @param string $url   Url
   * 
   * @return JooS_Stream_Wrapper_FS_Partition
   * @throws JooS_Stream_Wrapper_FS_Exception
   */
  protected static function getPartition($url) {
    $urlParts = explode("://", $url);
    $protocol = $urlParts[0];
    
    if (isset(self::$partitions[$protocol])) {
      return self::$partitions[$protocol];
    }
    else {
      require_once "JooS/Stream/Wrapper/FS/Exception.php";
      
      throw new JooS_Stream_Wrapper_FS_Exception(
        "Working with unregistered stream '$protocol://'"
      );
    }
  }
  
  /**
   * Return urlPath of url
   * 
   * @param string $url Url
   * 
   * @return string
   */
  protected static function getRelativePath($url) {
    $host = parse_url($url, PHP_URL_HOST);
    $path = parse_url($url, PHP_URL_PATH);
    
    return $host . (strlen($path) ? "/" . $path : "");
  }
  
  /**
   * Return real file path
   * 
   * @param string $url
   * 
   * @return string
   */
  protected static function getPath($url) {
    $path = null;
    
    $partition = self::getPartition($url);
    $relativePath = self::getRelativePath($url);
    
    $entity = $partition->getEntity($relativePath);
    if (!is_null($entity)) {
      $path = $entity->path();
    }
    
    return $path;
  }
}
