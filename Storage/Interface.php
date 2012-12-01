<?php

/**
 * @package JooS
 */

/**
 * Storage interface.
 */
interface JooS_Stream_Storage_Interface
{

  /**
   * Constructor
   * 
   * @param JooS_Stream_Entity_Interface $content Entity
   */
  public function __construct(JooS_Stream_Entity_Interface $content);
  
  /**
   * Returns entity
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function entity();

}
