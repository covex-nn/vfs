<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Entity/Interface.php";

/**
 * Stream Entity.
 */
abstract class JooS_Stream_Entity_Abstract implements JooS_Stream_Entity_Interface
{
  /**
   * @var string
   */
  private $_basename;
  
  /**
   * @var string
   */
  private $_path;

  /**
   * Protected constructor
   * 
   * @param string $basename Filename
   * @param string $path     Path
   */
  protected function __construct($basename, $path)
  {
    $this->_setBasename($basename);
    $this->_setPath($path);
  }

  /**
   * Returns basename of entity.
   * 
   * @return string
   */
  public function basename()
  {
    return $this->_basename;
  }

  /**
   * Returns path of entity.
   * 
   * @return string
   */
  public function path()
  {
    return $this->_path;
  }

  /**
   * Sets basename
   * 
   * @param string $basename File name
   * 
   * @return null
   */
  protected function _setBasename($basename) {
    $this->_basename = $basename;
  }
  
  /**
   * Sets path
   * 
   * @param string $path Path
   * 
   * @return null
   */
  protected function _setPath($path)
  {
    $unixPath = str_replace("\\", "/", $path);
    $this->_path = $unixPath;
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
