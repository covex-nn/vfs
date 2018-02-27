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
 * File/directory operations helper.
 */
class Files
{
    /**
     * @var string
     */
    private $systemTempDirectory = null;

    /**
     * @var int
     */
    private $tempnamCounter = 0;

    public function __destruct()
    {
        $dir = $this->sysGetTempDir(false);
        if (null !== $dir) {
            $this->delete($dir);
        }
    }

    /**
     * Creates directory in sys_get_temp_dir().
     */
    public function mkdir(int $mode = 0777): string
    {
        $name = $this->tempnam();
        mkdir($name, $mode);

        return $name;
    }

    /**
     * Get unique filename.
     */
    public function tempnam(): string
    {
        $sysTempDir = $this->sysGetTempDir();
        do {
            ++$this->tempnamCounter;
            $name = $sysTempDir.'/'.$this->tempnamCounter;
        } while (file_exists($name));

        return $name;
    }

    /**
     * Delete file or directory.
     */
    public function delete($path): void
    {
        if (is_link($path)) {
            $this->deleteSymlink($path);
        } elseif (is_file($path)) {
            unlink($path);
        } else {
            $iteratorRd = new \RecursiveDirectoryIterator($path);
            $iteratorRi = new \RecursiveIteratorIterator(
                $iteratorRd, \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($iteratorRi as $file) {
                /** @var \SplFileInfo $file */
                $filename = $file->getPathname();
                if ($file->isLink()) {
                    $this->deleteSymlink($filename);
                } elseif ($file->isDir()) {
                    if ('.' != substr($filename, -1, 1)) {
                        rmdir($filename);
                    }
                } else {
                    unlink($filename);
                }
            }
            rmdir($path);
        }
    }

    /**
     * Delete symlink.
     */
    private function deleteSymlink($link): void
    {
        clearstatcache();
        $target = @readlink($link);
        $newTarget = null;

        if (false !== $target && !file_exists($target)) {
            $target = false;
        }
        if (false !== $target) {
            do {
                $newTarget = dirname($target).'/'.uniqid(basename($target));
            } while (file_exists($newTarget));

            rename($target, $newTarget);
        }
        if (!@rmdir($link)) {
            unlink($link);
        }
        if (false !== $target && null !== $newTarget) {
            rename($newTarget, $target);
        }
    }

    /**
     * Get path to own temp directory.
     */
    private function sysGetTempDir(bool $create = true): ?string
    {
        if (null === $this->systemTempDirectory && $create) {
            $sysTmpDir = rtrim(sys_get_temp_dir(), '\\/');

            do {
                $name = $sysTmpDir.'/'.uniqid('fs', true);
            } while (file_exists($name));

            mkdir($name, 0777);
            $this->systemTempDirectory = $name;
        }

        return $this->systemTempDirectory;
    }
}
