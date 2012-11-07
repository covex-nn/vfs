<?php

/**
 * @package JooS
 */
final class JooS_Stream_Storage_List implements IteratorAggregate, Countable, ArrayAccess
{

  /**
   * @var ArrayObject
   */
  private $_items = null;

  /**
   * Constructor.
   */
  public function __construct()
  {
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
   * Is item exists ?
   * 
   * @param string $name
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
   * @param string $name
   *
   * @return JooS_Stream_Storage_Interface
   */
  public function __get($name)
  {
    return isset($this->_items[$name]) ? $this->_items[$name] : null;
  }

  /**
   * Set new item.
   * 
   * @param string $name
   * @param JooS_Stream_Storage_Interface $value
   * 
   * @return null
   */
  public function __set($name, $value)
  {
    if ($value instanceof JooS_Stream_Storage_Interface) {
      $this->_items[$name] = $value;
    } else {
      trigger_error("Type mismatch", E_WARNING);
    }
  }

  /**
   * Delete item.
   * 
   * @param type $name
   * 
   * @return null
   */
  public function __unset($name)
  {
    unset($this->_items[$name]);
  }

  /**
   * Is item exists ?
   * 
   * @param string $offset
   * 
   * @return boolean
   */
  public function offsetExists($offset)
  {
    return $this->__isset($offset);
  }

  /**
   * Returns item.
   * 
   * @param string $name
   *
   * @return JooS_Stream_Storage_Interface
   */
  public function offsetGet($offset)
  {
    return $this->__get($offset);
  }

  /**
   * Set new item.
   * 
   * @param string $name
   * @param JooS_Stream_Storage_Interface $value
   * 
   * @return null
   */
  public function offsetSet($offset, $value)
  {
    return $this->__set($offset, $value);
  }

  /**
   * Delete item.
   * 
   * @param type $name
   * 
   * @return null
   */
  public function offsetUnset($offset)
  {
    return $this->__unset($offset);
  }

}
