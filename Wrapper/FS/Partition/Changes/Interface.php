<?php

/**
 * @package JooS
 */

/**
 * Partition changes interface.
 */
interface JooS_Stream_Wrapper_FS_Partition_Changes_Interface
  extends Countable
{
  
  /**
   * Return stream storage
   * 
   * @param string $path Path
   * 
   * @return JooS_Stream_Storage_Interface
   */
  public function get($path);
  
  /**
   * Add stream storage to changes array
   * 
   * @param string                        $path    Path
   * @param JooS_Stream_Storage_Interface $storage Stream storage
   * 
   * @return boolean
   */
  public function add($path, JooS_Stream_Storage_Interface $storage);
  
  /**
   * Delete stream storage from array
   * 
   * @param string $path Path
   * 
   * @return boolean
   */
  public function delete($path);
  
  /**
   * Is $path added to array ?
   * 
   * @param string $path Path
   * 
   * @return boolean
   */
  public function exists($path);
  
}
