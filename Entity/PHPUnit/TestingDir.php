<?php

  require_once "JooS/Stream/Entity/Abstract.php";
  
  class JooS_Stream_Entity_PHPUnit_TestingDir extends JooS_Stream_Entity_Abstract {
    /**
     * @param string $name
     * @param string $path
     * 
     * @return JooS_Stream_Entity_PHPUnit_TestingFile
     */
    public static function newInstance($name, $path) {
      return new self($name, $path);
    }
    
    public function file_exists() {
      return true;
    }
    
    public function is_dir() {
      return true;
    }
    
    public function is_file() {
      return false;
    }
    
    public function is_readable() {
      return true;
    }
    
    public function is_writable() {
      return true;
    }
  }
