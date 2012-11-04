<?php

/**
 * @package JooS
 */
require_once "JooS/Stream/Entity/Interface.php";

abstract class JooS_Stream_Entity_Abstract implements JooS_Stream_Entity_Interface
{

    /**
     * @var string
     */
    private $_path;

    /**
     * @var bool
     */
    private $_virtual;

    /**
     * @param string $path
     */
    protected function __construct($path)
    {
        $this->_setPath($path);
        $this->_setVirtual(false);
    }

    /**
     * @return bool
     */
    public function is_virtual()
    {
        return $this->_virtual;
    }

    /**
     * @return string
     */
    public function basename()
    {
        return basename($this->path());
    }

    /**
     * @return string
     */
    public function dirname()
    {
        return dirname($this->path());
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->_path;
    }

    /**
     * @param string $path 
     */
    protected function _setPath($path)
    {
        $this->_path = $path;
    }

    /**
     * @param bool $virtual 
     */
    protected function _setVirtual($virtual)
    {
        $this->_virtual = !!$virtual;
    }

}
