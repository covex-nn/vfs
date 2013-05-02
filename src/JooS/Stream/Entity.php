<?php

/**
 * @package JooS
 * @subpackage Stream
 */
namespace JooS\Stream;

/**
 * Stream entity (real file/directory)
 */
class Entity extends Entity_Abstract
{
  /**
   * Create new entity instance
   * 
   * @param string $path Path to file
   * 
   * @return Entity
   */
  public static function newInstance($path)
  {
    $basename = basename($path);
    return new self($basename, $path);
  }
  
  /**
   * Fix slashes and backslashes in path
   * 
   * @param string $path Path
   * 
   * @return string
   */
  public static function fixPath($path)
  {
    if (strpos($path, "\\") !== false) {
      $path = str_replace("\\", "/", $path);
    }
    while (strpos($path, "//") !== false) {
      $path = str_replace("//", "/", $path);
    }
    if (strlen($path)) {
      if (substr($path, 0, 1) == "/") {
        $path = ltrim($path, "/");
      }
      if (substr($path, -1, 1) == "/") {
        $path = rtrim($path, "/");
      }
    }
    return $path;
  }
}
