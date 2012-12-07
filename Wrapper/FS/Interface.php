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
  
  /**
   * Removes a directory
   *
   * This method is called in response to rmdir().
   *
   * @param string $path    Path
   * @param int    $options Options
   * 
   * @link http://www.php.net/manual/en/streamwrapper.rmdir.php
   * @return boolean
   */
  public function rmdir($path, $options);
  
  /**
   * Delete a file
   * 
   * This method is called in response to unlink().
   * 
   * @param string $path Path
   * 
   * @link http://www.php.net/manual/en/streamwrapper.unlink.php
   * @return bool
   */
  public function unlink($path);
  
  /**
   * Renames a file or directory
   *
   * @param string $pathFrom Path from
   * @param string $pathTo   Path to
   * 
   * @link http://www.php.net/manual/en/streamwrapper.rename.php
   * @return boolean
   */
  public function rename($pathFrom, $pathTo);
  
  /**
   * Open directory handle
   *
   * This method is called in response to opendir().
   * 
   * @param string $path    Path
   * @param int    $options Options
   * 
   * @link http://www.php.net/manual/en/streamwrapper.dir-opendir.php
   * @return boolean
   * @todo JooS_Stream_Wrapper_FS
   */
  public function dir_opendir($path, $options);
  
  /**
   * Read entry from directory handle
   *
   * This method is called in response to readdir(). 
   *
   * @link http://www.php.net/manual/en/streamwrapper.dir-readdir.php
   * @return string
   * @todo JooS_Stream_Wrapper_FS
   */
  public function dir_readdir();
  
  /**
   * Close directory handle
   *
   * This method is called in response to closedir().
   * Any resources which were locked, or allocated, during opening and use of
   * the directory stream should be released.
   *
   * @link http://www.php.net/manual/en/streamwrapper.dir-closedir.php
   * @return boolean
   * @todo JooS_Stream_Wrapper_FS
   */
  public function dir_closedir();
  
  /**
   * Rewind directory handle
   *
   * This method is called in response to rewinddir().
   *
   * Should reset the output generated by stream::dir_readdir().
   * i.e.: The next call to stream::dir_readdir() should
   * return the first entry in the location returned by stream::dir_opendir().
   *
   * @link http://www.php.net/manual/en/streamwrapper.dir-rewinddir.php
   * @return boolean
   * @todo JooS_Stream_Wrapper_FS
   */
  public function dir_rewinddir();

  /**
   * Constructs a new stream wrapper
   * 
   * Called when opening the stream wrapper,
   * right before stream::stream_open().
   * 
   * @link http://www.php.net/manual/en/streamwrapper.construct.php
   */
  public function __construct();
  
  /**
   * Opens file or URL
   *
   * This method is called immediately after the wrapper is initialized
   * (f.e. by fopen() and file_get_contents()).
   *
   * @param string $path        Path
   * @param string $mode        Mode
   * @param int    $options     Options
   * @param string &$openedPath Opened Path
   * 
   * @link http://www.php.net/manual/en/streamwrapper.stream-open.php
   * @return boolean
   */
  public function stream_open($path, $mode, $options, &$openedPath);

  /**
   * Close an resource
   *
   * This method is called in response to fclose().
   *
   * All resources that were locked, or allocated, by the wrapper
   * should be released.
   *
   * @link http://www.php.net/manual/en/streamwrapper.stream-close.php
   * @return null
   */
  public function stream_close();
  
  /**
   * Retrieve information about a file resource
   * 
   * This method is called in response to fstat(). 
   *
   * @link http://www.php.net/manual/en/streamwrapper.stream-stat.php
   * @return array
   */
  public function stream_stat();
  
  /**
   * Read from stream
   * 
   * This method is called in response to fread() and fgets().
   *
   * @param int $count Count
   * 
   * @link http://www.php.net/manual/en/streamwrapper.stream-read.php
   * @return string
   */
  public function stream_read($count);

  /**
   * Tests for end-of-file on a file pointer
   *
   * This method is called in response to feof(). 
   *
   * @link http://www.php.net/manual/en/streamwrapper.stream-eof.php
   * @return bool
   */
  public function stream_eof();

  /**
   * Retrieve the current position of a stream
   *
   * This method is called in response to ftell(). 
   *
   * @link http://www.php.net/manual/en/streamwrapper.stream-tell.php
   * @return int
   */
  public function stream_tell();

  /**
   * Seeks to specific location in a stream
   * 
   * This method is called in response to fseek().
   *
   * The read/write position of the stream should be updated according to
   * the offset and whence.
   *
   * @param int $offset Offset
   * @param int $whence = SEEK_SET
   * 
   * @link http://www.php.net/manual/en/streamwrapper.stream-seek.php
   * @return boolean
   */
  public function stream_seek($offset, $whence = SEEK_SET);

  /**
   * Write to stream
   * 
   * This method is called in response to fwrite(). 
   * 
   * @param string $data Data
   * 
   * @link http://www.php.net/manual/en/streamwrapper.stream-write.php
   * @return int
   */
  public function stream_write($data);

  /**
   * Flushes the output
   * 
   * This method is called in response to fflush().
   *
   * If you have cached data in your stream but not yet stored it into the
   * underlying storage, you should do so now.
   *
   * @link http://www.php.net/manual/en/streamwrapper.stream-flush.php
   * @return boolean
   */
  public function stream_flush();
    
}
