<?php

  /**
   * @package JooS
   */
  interface JooS_Stream_Storage_Interface {
    /**
     * @return string
     */
    public function path();
    
    /**
     * @return string
     */
    public function name();
    
    /**
     * @return JooS_Stream_Storage_Interface
     */
    public function storage();
    
    /**
     * @return JooS_Stream_Entity_Interface
     */
    public function content();
  }
