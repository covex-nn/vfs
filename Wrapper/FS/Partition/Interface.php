<?php

/**
 * @package JooS
 */

/**
 * Partition interface
 */
interface JooS_Stream_Wrapper_FS_Partition_Interface {
  /**
   * Create a directory
   *
   * @param string $path    Path
   * @param int    $mode    Mode
   * @param int    $options Options
   * 
   * @return boolean
   */
  public function makeDirectory($path, $mode, $options);
  
  /**
   * Return file/directory entity
   * 
   * @param string $filename Path to file/directory
   * 
   * @return JooS_Stream_Entity_Interface
   */
  public function getEntity($filename);
}
