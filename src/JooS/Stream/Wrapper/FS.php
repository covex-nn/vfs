<?php

/**
 * Stream for local file system
 *
 * @author  Andrey F. Mindubaev <covex.mobile@gmail.com>
 * @license http://opensource.org/licenses/MIT  MIT License
 */
namespace JooS\Stream;

/**
 * Stream for local file system
 */
class Wrapper_FS extends Wrapper implements Wrapper_FS_Interface
{

  /**
   * Retrieve information about a file
   *
   * @param string $url   Url
   * @param int    $flags Flags
   *
   * @return array
   */
  public function url_stat($url, $flags)
  {
    $partition = self::getPartition($url);
    $path = self::getRelativePath($url);

    return $partition->getStat($path, $flags);
  }

  /**
   * Create a directory
   *
   * @param string $url     Path
   * @param int    $mode    Mode
   * @param int    $options Options
   *
   * @return boolean
   */
  public function mkdir($url, $mode, $options)
  {
    $partition = self::getPartition($url);
    $path = self::getRelativePath($url);

    return !!$partition->makeDirectory($path, $mode, $options);
  }

  /**
   * Removes a directory
   *
   * @param string $url     Path
   * @param int    $options Options
   *
   * @return boolean
   */
  public function rmdir($url, $options)
  {
    $partition = self::getPartition($url);
    $path = self::getRelativePath($url);

    return !!$partition->removeDirectory($path, $options);
  }

  /**
   * Delete a file
   *
   * @param string $url Path
   *
   * @return bool
   */
  public function unlink($url)
  {
    $partition = self::getPartition($url);
    $path = self::getRelativePath($url);

    return !!$partition->deleteFile($path);
  }

  /**
   * Renames a file or directory
   *
   * @param string $srcPath Source path
   * @param string $dstPath Destination path
   *
   * @return boolean
   */
  public function rename($srcPath, $dstPath)
  {
    $srcPartition = self::getPartition($srcPath);
    $dstPartition = self::getPartition($dstPath);

    if ($srcPartition != $dstPartition) {
      return false;
    }

    $srcRelativePath = self::getRelativePath($srcPath);
    $dstRelativePath = self::getRelativePath($dstPath);

    return !!$srcPartition->rename($srcRelativePath, $dstRelativePath);
  }

  /**
   * @var array
   */
  private $_dirFiles;

  /**
   * Open directory handle
   *
   * @param string $url Path
   *
   * @return boolean
   */
  public function dir_opendir($url)
  {
    $partition = self::getPartition($url);
    $path = self::getRelativePath($url);

    $files = $partition->getList($path);
    if (is_array($files)) {
      $this->_dirFiles = array();
      foreach ($files as $file) {
        /* @var $file Entity_Interface */
        $this->_dirFiles[] = $file->basename();
      }

      $result = true;
    } else {
      $result = false;
    }

    return $result;
  }

  /**
   * Read entry from directory handle
   *
   * @return string
   */
  public function dir_readdir()
  {
    $each = each($this->_dirFiles);

    if ($each === false) {
      $result = false;
    } else {
      $result = $each["value"];
    }

    return $result;
  }

  /**
   * Close directory handle
   *
   * @return boolean
   */
  public function dir_closedir()
  {
    unset($this->_dirFiles);

    return true;
  }

  /**
   * Rewind directory handle
   *
   * @return boolean
   */
  public function dir_rewinddir()
  {
    reset($this->_dirFiles);

    return true;
  }

  /**
   * @var resource
   */
  private $_filePointer;

  /**
   * @var Entity_Interface
   */
  private $_fileEntity;

  /**
   * Constructs a new stream wrapper
   */
  public function __construct()
  {
    $this->_filePointer = null;
    $this->_fileEntity = null;
  }

  /**
   * Opens file or URL
   *
   * @param string $url         Path
   * @param string $mode        Mode
   * @param int    $options     Options
   * @param string &$openedPath Opened Path
   *
   * @return boolean
   */
  public function stream_open($url, $mode, $options, &$openedPath)
  {
    $partition = self::getPartition($url);
    $path = self::getRelativePath($url);

    $this->_filePointer = $partition->fileOpen(
      $path, $mode, $options, $this->_fileEntity
    );

    $result = !!$this->_filePointer;
    if ($result && ($options & STREAM_USE_PATH)) {
      $openedPath = $path;
    }
    return $result;
  }

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
  public function stream_close()
  {
    fclose($this->_filePointer);
  }

  /**
   * Read from stream
   *
   * @param int $count Count
   *
   * @return string
   */
  public function stream_read($count)
  {
    return fread($this->_filePointer, $count);
  }

  /**
   * Retrieve information about a file resource
   *
   * @return array
   */
  public function stream_stat()
  {
    return fstat($this->_filePointer);
  }

  /**
   * Tests for end-of-file on a file pointer
   *
   * @return boolean
   */
  public function stream_eof()
  {
    return feof($this->_filePointer);
  }

  /**
   * Retrieve the current position of a stream
   *
   * @return int
   */
  public function stream_tell()
  {
    return ftell($this->_filePointer);
  }

  /**
   * Seeks to specific location in a stream
   *
   * @param int $offset Offset
   * @param int $whence = SEEK_SET
   *
   * @return boolean
   */
  public function stream_seek($offset, $whence = SEEK_SET)
  {
    return fseek($this->_filePointer, $offset, $whence);
  }

  /**
   * Write to stream
   *
   * @param string $data Data
   *
   * @return int
   */
  public function stream_write($data)
  {
    return fwrite($this->_filePointer, $data);
  }

  /**
   * Flushes the output
   *
   * @return boolean
   */
  public function stream_flush()
  {
    return fflush($this->_filePointer);
  }

  /**
   * @var array
   */
  protected static $_partitions = array();

  /**
   * Register stream wrapper
   *
   * @param string  $protocol Protocol name
   * @param string  $root     FS root directory
   * @param integer $flags    Stream flags
   *
   * @return boolean
   */
  public static function register($protocol, $root = null, $flags = 0)
  {
    $result = parent::register($protocol, $flags);
    if ($result) {
      if (!is_null($root)) {
        $content = Entity::newInstance($root);
      } else {
        $content = null;
      }

      $partition = new Wrapper_FS_Partition($content);

      self::$_partitions[$protocol] = $partition;
    }
    return $result;
  }

  /**
   * Commit all changes to real FS
   *
   * @param string $protocol Protocol name
   *
   * @return boolean
   */
  public static function commit($protocol)
  {
    if (isset(self::$_partitions[$protocol])) {
      $partition = self::$_partitions[$protocol];
      /* @var $partition Wrapper_FS_Partition */
      $partition->commit();

      $result = true;
    } else {
      $result = false;
    }

    return $result;
  }

  /**
   * Unregister stream wrapper
   *
   * @param string $protocol Protocol name
   *
   * @return boolean
   */
  public static function unregister($protocol)
  {
    unset(self::$_partitions[$protocol]);

    return parent::unregister($protocol);
  }

  /**
   * Return partition by file url
   *
   * @param string $url Url
   *
   * @return Wrapper_FS_Partition
   */
  protected static function getPartition($url)
  {
    $urlParts = explode("://", $url);
    $protocol = array_shift($urlParts);

    return self::$_partitions[$protocol];
  }

  /**
   * Return urlPath of url
   *
   * @param string $url Url
   *
   * @return string
   */
  public static function getRelativePath($url)
  {
    $urlParts = explode("://", $url);
    array_shift($urlParts);
    $urlPath = implode("://", $urlParts);

    return Entity::fixPath($urlPath);
  }

}
