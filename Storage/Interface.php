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
   * Returns name.
   * 
   * @return string
   */
  public function name();

  /**
   * Returns storage.
   * 
   * @return JooS_Stream_Storage_Interface
   */
  public function storage();

  /**
   * Returns entiry.
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function content();

}
