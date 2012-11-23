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
   * Returns storage path.
   * 
   * @return string
   */
  public function path();

  /**
   * Return name
   * 
   * @return string
   */
  public function name();

  /**
   * Return parent storage
   * 
   * @return JooS_Stream_Storage_Interface
   */
  public function storage();

  /**
   * Returns entity
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function content();

}
