<?php
  
  /**
   * @package JooS
   */

  final class JooS_Stream_Storage_List implements IteratorAggregate, Countable, ArrayAccess {
    /**
     * @var ArrayObject
     */
    private $_items = null;
    
    public function __construct() {
      $this->_items = new ArrayObject(array());
    }

    /**
     * @return ArrayIterator 
     */
    public function getIterator() {
      return $this->_items->getIterator();
    }
    
    /**
     * @return int
     */
    public function count() {
      return $this->_items->count();
    }
    
    public function __isset($name) {
      return isset($this->_items[$name]);
    }
    
    public function __get($name) {
      return isset($this->_items[$name]) ? $this->_items[$name] : null;
    }
    
    public function __set($name, $value) {
      if ($value instanceof JooS_Stream_Storage_Interface) {
        $this->_items[$name] = $value;
      }
      else {
        trigger_error("Type mismatch", E_WARNING);
      }
    }
    
    public function __unset($name) {
      unset($this->_items[$name]);
    }
    
    public function offsetExists($offset) {
      return $this->__isset($offset);
    }
    
    public function offsetGet($offset) {
      return $this->__get($offset);
    }
    
    public function offsetSet($offset, $value) {
      return $this->__set($offset, $value);
    }
    
    public function offsetUnset($offset) {
      return $this->__unset($offset);
    }
  }
