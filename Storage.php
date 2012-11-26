<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Storage/Interface.php";

/**
 * Abstract stream storage.
 */
abstract class JooS_Stream_Storage implements JooS_Stream_Storage_Interface
{

  /**
   * Parent storage
   * 
   * @var JooS_Stream_Storage_Dir
   */
  private $_storage = null;

  /**
   * @var JooS_Stream_Entity_Interface
   */
  private $_content = null;

  /**
   * Creates new instance.
   * 
   * @param JooS_Stream_Entity_Interface $content Content
   * @param JooS_Stream_Storage_Dir      $storage Parent storage
   * 
   * @return JooS_Stream_Storage
   */
  public static function newInstance(JooS_Stream_Entity_Interface $content, JooS_Stream_Storage_Dir $storage = null)
  {
    if ($content->is_dir()) {
      require_once "JooS/Stream/Storage/Dir.php";

      $instance = new JooS_Stream_Storage_Dir();
    } else {
      require_once "JooS/Stream/Storage/File.php";

      $instance = new JooS_Stream_Storage_File();
    }

    $instance->_setContent($content);
    if (!is_null($storage)) {
      $instance->_setStorage($storage);
      
      $storage->add($instance);
    }

    return $instance;
  }

  /**
   * Protected constructor
   */
  protected function __construct()
  {
    
  }

  /**
   * Return path
   * 
   * @return string
   */
  final public function path()
  {
    $storage = $this->storage();
    
    if (is_null($storage)) {
      $path = "";
    } else {
      $path = $storage->path();
    }

    return $path . "/" . $this->name();
  }

  /**
   * Return name
   * 
   * @return string
   */
  final public function name()
  {
    return $this->content()->basename();
  }

  /**
   * Returns entity
   * 
   * @return JooS_Stream_Entity_Interface
   */
  final public function content()
  {
    return $this->_content;
  }

  /**
   * Return parent storage
   * 
   * @return JooS_Stream_Storage_Dir
   */
  final public function storage()
  {
    return $this->_storage;
  }

  /**
   * Sets entity
   * 
   * @param JooS_Stream_Entity_Interface $content Content
   * 
   * @return null
   */
  protected function _setContent(JooS_Stream_Entity_Interface $content)
  {
    $this->_content = $content;
  }

  /**
   * Sets parent storage
   * 
   * @param JooS_Stream_Storage_Dir $storage Storage
   * 
   * @return null
   */
  protected function _setStorage(JooS_Stream_Storage_Dir $storage)
  {
    $this->_storage = $storage;
  }

}
