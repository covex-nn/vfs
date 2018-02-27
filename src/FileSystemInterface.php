<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Stream;

/**
 * FS stream wrapper interface.
 */
interface FileSystemInterface
{
    /**
     * Constructs a new stream wrapper.
     *
     * Called when opening the stream wrapper,
     * right before stream::stream_open().
     *
     * @see http://www.php.net/manual/en/streamwrapper.construct.php
     */
    public function __construct();

    /**
     * Retrieve information about a file.
     *
     * @return array|bool
     */
    public function url_stat(string $url, int $flags);

    /**
     * Create a directory. This method is called in response to mkdir().
     *
     * @return bool
     *
     * @see http://www.php.net/manual/en/streamwrapper.mkdir.php
     */
    public function mkdir(string $path, int $mode, int $options): bool;

    /**
     * Removes a directory. This method is called in response to rmdir().
     *
     * @see http://www.php.net/manual/en/streamwrapper.rmdir.php
     */
    public function rmdir(string $path, int $options): bool;

    /**
     * Delete a file. This method is called in response to unlink().
     *
     * @see http://www.php.net/manual/en/streamwrapper.unlink.php
     */
    public function unlink(string $path): bool;

    /**
     * Renames a file or directory.
     *
     * @see http://www.php.net/manual/en/streamwrapper.rename.php
     */
    public function rename(string $pathFrom, string $pathTo): bool;

    /**
     * Open directory handle. This method is called in response to opendir().
     *
     * @see http://www.php.net/manual/en/streamwrapper.dir-opendir.php
     */
    public function dir_opendir(string $path): bool;

    /**
     * Read entry from directory handle. This method is called in response to readdir().
     *
     * @return string|bool
     *
     * @see http://www.php.net/manual/en/streamwrapper.dir-readdir.php
     */
    public function dir_readdir();

    /**
     * Close directory handle. This method is called in response to closedir().
     *
     * @see http://www.php.net/manual/en/streamwrapper.dir-closedir.php
     */
    public function dir_closedir(): bool;

    /**
     * Rewind directory handle. This method is called in response to rewinddir().
     *
     * @see http://www.php.net/manual/en/streamwrapper.dir-rewinddir.php
     */
    public function dir_rewinddir(): bool;

    /**
     * Opens file or URL. This method is called immediately after the wrapper is initialized.
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-open.php
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool;

    /**
     * Close an resource. This method is called in response to fclose().
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-close.php
     */
    public function stream_close(): void;

    /**
     * Retrieve information about a file resource. This method is called in response to fstat().
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-stat.php
     */
    public function stream_stat(): array;

    /**
     * Read from stream. This method is called in response to fread() and fgets().
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-read.php
     */
    public function stream_read(int $count): string;

    /**
     * Tests for end-of-file on a file pointer. This method is called in response to feof().
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-eof.php
     */
    public function stream_eof(): bool;

    /**
     * Retrieve the current position of a stream. This method is called in response to ftell().
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-tell.php
     */
    public function stream_tell(): int;

    /**
     * Seeks to specific location in a stream. This method is called in response to fseek().
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-seek.php
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool;

    /**
     * Write to stream. This method is called in response to fwrite().
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-write.php
     */
    public function stream_write(string $data): int;

    /**
     * Flushes the output. This method is called in response to fflush().
     *
     * @see http://www.php.net/manual/en/streamwrapper.stream-flush.php
     */
    public function stream_flush(): bool;
}
