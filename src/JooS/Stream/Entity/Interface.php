<?php

/**
 * Interface for all stream entities.
 *
 * @author  Andrey F. Mindubaev <covex.mobile@gmail.com>
 * @license http://opensource.org/licenses/MIT  MIT License
 */
namespace JooS\Stream;

/**
 * Interface for all stream entities.
 *
 * @todo описать функции-операции над сущностью
 */
interface Entity_Interface
{

  /**
   * File exists ?
   *
   * @return boolean
   */
  public function file_exists();

  /**
   * Is entity - directory ?
   *
   * @return boolean
   */
  public function is_dir();

  /**
   * Is entity - file ?
   *
   * @return boolean
   */
  public function is_file();

  /**
   * Is entity - readable ?
   *
   * @return boolean
   */
  public function is_readable();

  /**
   * Is entity - writable ?
   *
   * @return boolean
   */
  public function is_writable();

  /**
   * Returns basename of entity.
   *
   * @return string
   */
  public function basename();

  /**
   * Returns path of entity.
   *
   * @return string
   */
  public function path();

}
