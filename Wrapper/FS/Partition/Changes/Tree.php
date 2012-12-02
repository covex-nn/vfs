<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Wrapper/FS/Partition/Changes/Interface.php";

/**
 * Partition changes, tree data.
 */
class JooS_Stream_Wrapper_FS_Partition_Changes_Tree
  implements JooS_Stream_Wrapper_FS_Partition_Changes_Interface
{
  /**
   * @var array
   */
  private $_ownData;
  
  /**
   * @var array
   */
  private $_subTrees;
  
  public function __construct()
  {
    $this->_ownData = array();
    $this->_subTrees = array();
  }
  
  /**
   * Return stream entiry
   * 
   * @param string $path Path
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function get($path)
  {
    $entity = null;
    
    $name = null;
    $subtree = $this->subtree($path, $name);
    if (!is_null($subtree) && isset($subtree->_ownData[$name])) {
      $entity = $subtree->_ownData[$name];
    }
    
    return $entity;
  }
  
  /**
   * Add stream entity to changes array
   * 
   * @param string                       $path   Path
   * @param JooS_Stream_Entity_Interface $entity Stream entity
   * 
   * @return boolean
   */
  public function add($path, JooS_Stream_Entity_Interface $entity)
  {
    $result = false;
    
    if (strlen($path)) {
      $name = null;
      $subtree = $this->subtree($path, $name, true);
      if (!isset($subtree->_ownData[$name])) {
        $subtree->_ownData[$name] = $entity;
        $result = true;
      }
    }
    
    return $result;
  }
  
  /**
   * Delete stream entity from array
   * 
   * @param string $path Path
   * 
   * @return boolean
   */
  public function delete($path)
  {
    $result = false;
    $parts = $this->split($path);
    
    $name = array_shift($parts);
    if (!sizeof($parts)) {
      if (isset($this->_ownData[$name])) {
        unset($this->_ownData[$name]);
        $result = true;
      }
    } elseif (isset($this->_subTrees[$name])) {
      $subtree = $this->_subTrees[$name];
      /* @var $subtree JooS_Stream_Wrapper_FS_Partition_Changes_Tree */
      
      $result = $subtree->delete($parts);
      if ($result && !$subtree->count()) {
        unset($this->_subTrees[$name]);
      }
    }
    
    return $result;
  }
  
  /**
   * Is $path added to array ?
   * 
   * @param string $path Path
   * 
   * @return boolean
   */
  public function exists($path)
  {
    $name = null;
    $subtree = $this->subtree($path, $name);
    
    return !is_null($subtree) && isset($subtree->_ownData[$name]);
  }
  
  /**
   * Count elements
   * 
   * @return int
   */
  public function count() {
    return sizeof($this->_ownData) + sizeof($this->_subTrees);
  }
  
  public function own($path = "") {
    if ($path) {
      $parts = $this->split($path);
      $name = array_shift($parts);
      
      $own = array();
      if (isset($this->_subTrees[$name])) {
        $subtree = $this->_subTrees[$name];
        /* @var $subtree JooS_Stream_Wrapper_FS_Partition_Changes_Tree */
        $this->_appendChildren(
          $own, $name, $subtree->own($parts)
        );
      }
    }
    else {
      $own = $this->_ownData;
    }
    return $own;
  }
  
  /**
   * Return all children in path/*.*
   * 
   * @param string $path Path
   * 
   * @return array
   */
  public function children($path = "") {
    if ($path) {
      $parts = $this->split($path);
      $name = array_shift($parts);
      
      $children = array();
      if (isset($this->_subTrees[$name])) {
        $subtree = $this->_subTrees[$name];
        /* @var $subtree JooS_Stream_Wrapper_FS_Partition_Changes_Tree */
        $this->_appendChildren(
          $children, $name, $subtree->children($parts)
        );
      }
    } else {
      $children = $this->own();
      foreach ($this->_subTrees as $name => $subtree) {
        /* @var $subtree JooS_Stream_Wrapper_FS_Partition_Changes_Tree */
        $this->_appendChildren(
          $children, $name, $subtree->children()
        );
      }
    }
    
    return $children;
  }

  /**
   * Add new children to array
   * 
   * @param array  $children  Children array
   * @param string $name      Current name
   * @param array  $_children Children of subtrees
   * 
   * @return null
   */
  private function _appendChildren(array &$children, $name, array $_children) {
    foreach ($_children as $key => $value) {
      $children[$name . "/" . $key] = $value;
    }
  }
  
  /**
   * Return subtree by path
   * 
   * @param string  $path   Path
   * @param string  &$name  Name of new element
   * @param boolean $create Auto create subtree ?
   * 
   * @return JooS_Stream_Wrapper_FS_Partition_Changes_Tree
   */
  protected function subtree($path, &$name, $create = false) {
    $parts = $this->split($path);
    
    $_name = array_shift($parts);
    if (!sizeof($parts)) {
      $name = $_name;
      $subtree = $this;
    } else {
      
      if (!isset($this->_subTrees[$_name]) && !$create) {
        $subtree = null;
        $name = null;
      } else {
        if ($create) {
          $this->_subTrees[$_name] = new self();
        }
        $subtree = $this->_subTrees[$_name]->subtree($parts, $name, $create);
      }
      
    }
    return $subtree;
  }
  
  /**
   * Split path into dir names
   * 
   * @param string $path Path
   * 
   * @return array
   */
  protected function split($path) {
    if (is_array($path)) {
      $parts = $path;
    } else {
      $parts = explode("/", $path);
    }
    return $parts;
  }
}
