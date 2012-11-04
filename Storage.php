<?php

require_once "JooS/Stream/Storage/Interface.php";

/**
 * @package JooS
 */
abstract class JooS_Stream_Storage implements JooS_Stream_Storage_Interface
{

    /**
     * @var string
     */
    private $_name = null;

    /**
     * Parent storage
     * 
     * @var JooS_Stream_Storage_Dir
     */
    private $_storage = null;

    /**
     * @var JooS_Stream_Entity_Interface
     */
    private $_content = null;

    /**
     * @param JooS_Stream_Entity_Interface $content
     * @param JooS_Stream_Storage_Dir $storage
     * @return JooS_Stream_Storage
     */
    public static function newInstance(JooS_Stream_Entity_Interface $content, JooS_Stream_Storage_Dir $storage = null)
    {
        if ($content->is_dir()) {
            require_once "JooS/Stream/Storage/Dir.php";

            $instance = new JooS_Stream_Storage_Dir();
        } else {
            require_once "JooS/Stream/Storage/File.php";

            $instance = new JooS_Stream_Storage_File();
        }

        $instance->_setName($content->basename());
        $instance->_setContent($content);
        if (!is_null($storage)) {
            $instance->_setStorage($storage);
        }

        return $instance;
    }

    protected function __construct()
    {
        
    }

    /**
     * @return string
     */
    public function path()
    {
        $storage = $this->storage();

        $path = "";
        if (!is_null($storage)) {
            $path = $storage->path() . "/";
        }

        return $path . $this->name();
    }

    /**
     * @return string
     */
    final public function name()
    {
        return $this->_name;
    }

    /**
     * @param string $name 
     */
    protected function _setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return JooS_Stream_Storage_Dir
     */
    final public function storage()
    {
        return $this->_storage;
    }

    /**
     * @param JooS_Stream_Storage_Dir $storage 
     */
    protected function _setStorage(JooS_Stream_Storage_Dir $storage)
    {
        $this->_storage = $storage;

        $files = $storage->files();
        $files[$this->name()] = $this;
    }

    /**
     * @return JooS_Stream_Entity_Interface
     */
    public function content()
    {
        return $this->_content;
    }

    /**
     * @param JooS_Stream_Entity_Interface $content 
     */
    protected function _setContent(JooS_Stream_Entity_Interface $content)
    {
        $this->_content = $content;
    }

}
