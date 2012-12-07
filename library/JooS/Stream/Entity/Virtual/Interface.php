<?php

/**
 * @package JooS
 */

/**
 * Interface for virtual stream entities
 */
interface JooS_Stream_Entity_Virtual_Interface
{
  /**
   * Return saved old entity
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function getRealEntity();
}
