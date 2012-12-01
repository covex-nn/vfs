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
}
