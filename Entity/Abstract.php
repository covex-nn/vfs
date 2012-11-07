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
  private $_path;

  /**
   * @var boolean
   */
  private $_virtual;

  /**
   * Protected constructor.
   * 
   * @param string $path
   */
  protected function __construct($path)
  {
    $this->_setPath($path);
    $this->_setVirtual(false);
  }

  /**
   * Is entity virtual ?
   * 
   * @return boolean
   */
  public function is_virtual()
  {
    return $this->_virtual;
  }

  /**
   * Returns basename of entity.
   * 
   * @return string
   */
  public function basename()
  {
    return basename($this->path());
  }

  /**
   * Returns dirname of entity.
   * 
   * @return string
   */
  public function dirname()
  {
    return dirname($this->path());
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
   * Sets path.
   * 
   * @param string $path 
   * 
   * @return null
   */
  protected function _setPath($path)
  {
    $this->_path = $path;
  }

  /**
   * Sets virtual flag.
   * 
   * @param boolean $virtual
   * 
   * @return null
   */
  protected function _setVirtual($virtual)
  {
    $this->_virtual = !!$virtual;
  }

}
