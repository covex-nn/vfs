<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Storage/Interface.php";

/**
 * Abstract stream storage.
 */
class JooS_Stream_Storage implements JooS_Stream_Storage_Interface
{

  /**
   * @var JooS_Stream_Entity_Interface
   */
  private $_content = null;

  /**
   * Protected constructor
   * 
   * @param JooS_Stream_Entity_Interface $entity Stream entity
   */
  public function __construct(JooS_Stream_Entity_Interface $entity)
  {
    $this->setEntity($entity);
  }

  /**
   * Returns entity
   * 
   * @return JooS_Stream_Entity_Interface
   */
  final public function entity()
  {
    return $this->_content;
  }

  /**
   * Sets entity
   * 
   * @param JooS_Stream_Entity_Interface $content Content
   * 
   * @return null
   */
  public function setEntity(JooS_Stream_Entity_Interface $content)
  {
    $this->_content = $content;
  }

}
