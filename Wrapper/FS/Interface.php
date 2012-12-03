<?php

/**
 * @package JooS
 */

/**
 * FS stream wrapper interface
 */
interface JooS_Stream_Wrapper_FS_Interface {
  /**
   * Retrieve information about a file
   *
   * @param string $url   Url
   * @param int    $flags Flags
   * 
   * @return array
   */
  public function url_stat($url, $flags);
  
  /**
   * Create a directory
   *
   * This method is called in response to mkdir().
   *
   * @param string $path    Path
   * @param int    $mode    Mode
   * @param int    $options Options
   * 
   * @link http://www.php.net/manual/en/streamwrapper.mkdir.php
   * @return boolean
   * @todo JooS_Stream_Wrapper_FS
   */
  public function mkdir($path, $mode, $options);
}
