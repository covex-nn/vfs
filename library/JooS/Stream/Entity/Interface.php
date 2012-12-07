<?php

/**
 * @package JooS
 */

/**
 * Interface for all stream entities.
 * 
 * @todo описать функции-операции над сущностью
 */
interface JooS_Stream_Entity_Interface
{

  const NONE = "None";

  const FILE = "File";

  const DIR = "Dir";

  const UNKNOWN = "Unknown";

  /**
   * File exists ?
   * 
   * @return booleans
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
