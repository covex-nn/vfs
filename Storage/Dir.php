<?php

  /**
   * @package JooS
   */

  require_once "JooS/Stream/Storage.php";
  
  final class JooS_Stream_Storage_Dir extends JooS_Stream_Storage implements IteratorAggregate, Countable {
    /**
     * @var JooS_Stream_Storage_List
     */
    private $_files = null;
    
    protected function  __construct() {
      parent::__construct();
            
      require_once "JooS/Stream/Storage/List.php";
      
      $this->_setFiles(new JooS_Stream_Storage_List());
    }
    
    /**
     * @return JooS_Stream_Storage_List
     */
    public function files() {
      return $this->_files;
    }
    
    /**
     * @param JooS_Stream_Storage_List $files 
     */
    protected function _setFiles(JooS_Stream_Storage_List $files) {
      $this->_files = $files;
    }
    
    /**
     * @return ArrayIterator 
     */
    public function getIterator() {
      return $this->files()->getIterator();
    }
    
    /**
     * @return int
     */
    public function count() {
      return $this->files()
        ->count();
    }
  }
