<?php

/**
 * @package JooS
 * 
 * @todo описать функции-операции над сущностью
 */
interface JooS_Stream_Entity_Interface
{

    const NONE = "None";
    const FILE = "File";
    const DIR = "Dir";
    const UNKNOWN = "Unknown";

    public function file_exists();

    public function is_dir();

    public function is_file();

    public function is_readable();

    public function is_writable();

    public function is_virtual();

    public function basename();

    public function dirname();

    public function path();

}
