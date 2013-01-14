<?php

/**
 * @package JooS
 * @subpackage Stream
 */
namespace JooS\Stream;

/**
 * Interface for virtual stream entities
 */
interface Entity_Virtual_Interface
{
  /**
   * Return saved old entity
   * 
   * @return Entity_Interface
   */
  public function getRealEntity();
}
