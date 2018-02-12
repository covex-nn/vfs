<?php

/**
 * Stream for local file system.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream;

use Covex\Stream\File\Entity;
use Covex\Stream\File\EntityInterface;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
class FileSystem implements FileSystemInterface
{
    /**
     * @var array
     */
    protected static $partitions = [];

    /**
     * @var array
     */
    private $dirFiles;

    /**
     * @var resource
     */
    private $filePointer;

    /**
     * @var EntityInterface
     */
    private $fileEntity;

    /**
     * Constructs a new stream wrapper.
     */
    public function __construct()
    {
        $this->filePointer = null;
        $this->fileEntity = null;
    }

    public function url_stat(string $url, int $flags)
    {
        $partition = static::getPartition($url);
        $path = static::getRelativePath($url);

        return $partition->getStat($path, $flags);
    }

    public function mkdir(string $url, int $mode, int $options): bool
    {
        $partition = self::getPartition($url);
        $path = self::getRelativePath($url);

        return (bool) $partition->makeDirectory($path, $mode, $options);
    }

    public function rmdir(string $url, int $options): bool
    {
        $partition = self::getPartition($url);
        $path = self::getRelativePath($url);

        return (bool) $partition->removeDirectory($path, $options);
    }

    public function unlink(string $url): bool
    {
        $partition = self::getPartition($url);
        $path = self::getRelativePath($url);

        return (bool) $partition->deleteFile($path);
    }

    public function rename(string $srcPath, string $dstPath): bool
    {
        $srcPartition = self::getPartition($srcPath);
        $dstPartition = self::getPartition($dstPath);

        if ($srcPartition !== $dstPartition) {
            return false;
        }

        $srcRelativePath = self::getRelativePath($srcPath);
        $dstRelativePath = self::getRelativePath($dstPath);

        return (bool) $srcPartition->rename($srcRelativePath, $dstRelativePath);
    }

    public function dir_opendir(string $url): bool
    {
        $partition = self::getPartition($url);
        $path = self::getRelativePath($url);

        $files = $partition->getList($path);
        if (is_array($files)) {
            $this->dirFiles = [];
            foreach ($files as $file) {
                /* @var $file EntityInterface */
                $this->dirFiles[] = $file->basename();
            }
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    public function dir_readdir()
    {
        $each = each($this->dirFiles);

        if (false === $each) {
            $result = false;
        } else {
            $result = $each['value'];
        }

        return $result;
    }

    public function dir_closedir(): bool
    {
        unset($this->dirFiles);

        return true;
    }

    public function dir_rewinddir(): bool
    {
        reset($this->dirFiles);

        return true;
    }

    public function stream_open(string $url, string $mode, int $options, ?string &$openedPath): bool
    {
        $partition = self::getPartition($url);
        $path = self::getRelativePath($url);

        $this->filePointer = $partition->fileOpen(
            $path, $mode, $options
        );

        $result = (bool) $this->filePointer;
        if ($result && ($options & STREAM_USE_PATH)) {
            $openedPath = $path;
        }

        return $result;
    }

    public function stream_close(): void
    {
        fclose($this->filePointer);
    }

    public function stream_read(int $count): string
    {
        return fread($this->filePointer, $count);
    }

    public function stream_stat(): array
    {
        return fstat($this->filePointer);
    }

    public function stream_eof(): bool
    {
        return feof($this->filePointer);
    }

    public function stream_tell(): int
    {
        return ftell($this->filePointer);
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return 0 === fseek($this->filePointer, $offset, $whence);
    }

    public function stream_write(string $data): int
    {
        return fwrite($this->filePointer, $data);
    }

    public function stream_flush(): bool
    {
        return fflush($this->filePointer);
    }

    /**
     * Register stream wrapper.
     *
     * @param string $protocol Protocol name
     * @param string $root     FS root directory
     * @param int    $flags    Stream flags
     *
     * @throws Exception
     * @return bool
     */
    public static function register(string $protocol, string $root = null, int $flags = 0): bool
    {
        $wrappers = stream_get_wrappers();
        if (in_array($protocol, $wrappers)) {
            throw new Exception(
                "Protocol '$protocol' has been already registered"
            );
        }
        $wrapper = stream_wrapper_register($protocol, get_called_class(), $flags);

        if ($wrapper) {
            if (null !== $root) {
                $content = Entity::newInstance($root);
            } else {
                $content = null;
            }

            $partition = new Partition($content);

            self::$partitions[$protocol] = $partition;
        }

        return $wrapper;
    }

    /**
     * Commit all changes to real FS.
     *
     * @param string $protocol Protocol name
     *
     * @return bool
     */
    public static function commit(string $protocol): bool
    {
        if (isset(self::$partitions[$protocol])) {
            $partition = self::$partitions[$protocol];
            /* @var $partition Partition */
            $partition->commit();

            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    public static function unregister(string $protocol): bool
    {
        unset(self::$partitions[$protocol]);

        $wrappers = stream_get_wrappers();
        if (!in_array($protocol, $wrappers)) {
            throw new Exception(
                "Protocol '$protocol' has not been registered yet"
            );
        }

        return stream_wrapper_unregister($protocol);
    }

    /**
     * Return urlPath of url.
     *
     * @param string $url Url
     *
     * @return string
     */
    public static function getRelativePath(string $url): string
    {
        $urlParts = explode('://', $url);
        array_shift($urlParts);
        $urlPath = implode('://', $urlParts);

        return Entity::fixPath($urlPath);
    }

    /**
     * Return partition by file url.
     *
     * @param string $url Url
     *
     * @return Partition
     */
    protected static function getPartition(string $url): Partition
    {
        $urlParts = explode('://', $url);
        $protocol = array_shift($urlParts);

        return self::$partitions[$protocol];
    }
}
