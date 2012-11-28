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
  
  /**
   * Is entity - writable ?
   * 
   * @return boolean
   */
  public function is_writable()
  {
    $path = $this->path();
    return is_writable($path);
  }
  
  /**
   * Is entity - readable ?
   * 
   * @return boolean
   */
  public function is_readable()
  {
    $path = $this->path();
    return is_readable($path);
  }
  
  /**
   * Is entity - directory ?
   * 
   * @return boolean
   */
  public function is_dir()
  {
    $path = $this->path();
    return is_dir($path);
  }
  
  /**
   * Is entity - file ?
   * 
   * @return boolean
   */
  public function is_file()
  {
    $path = $this->path();
    return is_file($path);
  }
  
  /**
   * File exists ?
   * 
   * @return booleans
   */
  public function file_exists()
  {
    $path = $this->path();
    return file_exists($path);
  }
}
