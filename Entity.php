<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Entity/Abstract.php";

/**
 * Stream entity (real file/directory)
 */
class JooS_Stream_Entity extends JooS_Stream_Entity_Abstract
{
  /**
   * Create new entity instance
   * 
   * @param string $path Path to file
   * 
   * @return JooS_Stream_Entity
   */
  public static function newInstance($path)
  {
    $basename = basename($path);
    return new self($basename, $path);
  }
  
}
