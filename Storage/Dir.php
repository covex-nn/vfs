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
   * @var JooS_Stream_Storage_List
   */
  private $_files = null;

  /**
   * Constructor.
   */
  protected function __construct()
  {
    parent::__construct();

    require_once "JooS/Stream/Storage/List.php";

    $this->_setFiles(new JooS_Stream_Storage_List());
  }

  /**
   * Returns all files.
   * 
   * @return JooS_Stream_Storage_List
   */
  public function files()
  {
    return $this->_files;
  }

  /**
   * Sets file list.
   * 
   * @param JooS_Stream_Storage_List $files Files
   * 
   * @return null
   */
  protected function _setFiles(JooS_Stream_Storage_List $files)
  {
    $this->_files = $files;
  }

  /**
   * Iterator.
   * 
   * @return ArrayIterator 
   */
  public function getIterator()
  {
    return $this->files()->getIterator();
  }

  /**
   * Return number of files.
   * 
   * @return int
   */
  public function count()
  {
    return $this->files()
        ->count();
  }

}
