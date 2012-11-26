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
    $this->_path = $path;
  }

}
