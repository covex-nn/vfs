<?php

/**
 * Interface for virtual stream entities
 *
 * @author  Andrey F. Mindubaev <covex.mobile@gmail.com>
 * @license http://opensource.org/licenses/MIT  MIT License
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
