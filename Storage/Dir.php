<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Storage.php";

/**
 * Directory.
 */
final class JooS_Stream_Storage_Dir extends JooS_Stream_Storage
  implements IteratorAggregate, Countable
{

  /**
   * @var ArrayObject
   */
  private $_items = null;

  /**
   * Constructor.
   */
  protected function __construct()
  {
    parent::__construct();

    $this->_items = new ArrayObject(array());
  }

  /**
   * Iterator.
   * 
   * @return ArrayIterator 
   */
  public function getIterator()
  {
    return $this->_items->getIterator();
  }

  /**
   * Number of items.
   * 
   * @return int
   */
  public function count()
  {
    return $this->_items->count();
  }

  /**
   * Add new storage to list.
   * 
   * @param JooS_Stream_Storage_Interface $value
   * 
   * @return null
   */
  public function add(JooS_Stream_Storage_Interface $value) {
    $this->_items[$value->name()] = $value;
  }

  /**
   * Is item exists ?
   * 
   * @param string $name Name
   * 
   * @return boolean
   */
  public function __isset($name)
  {
    return isset($this->_items[$name]);
  }

  /**
   * Returns item.
   * 
   * @param string $name Name
   *
   * @return JooS_Stream_Storage_Interface
   */
  public function __get($name)
  {
    return isset($this->_items[$name]) ? $this->_items[$name] : null;
  }

  /**
   * Delete item.
   * 
   * @param type $name Name
   * 
   * @return null
   */
  public function __unset($name)
  {
    unset($this->_items[$name]);
  }
  
}
