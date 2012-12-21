<?php

/**
 * @package JooS
 * @subpackage Stream
 */
require_once "JooS/Stream/Wrapper/FS/Partition.php";

/**
 * Virtual filesystem tree
 */
class JooS_Stream_Wrapper_FS_Partition_Virtual
  extends JooS_Stream_Wrapper_FS_Partition
{
  
  /**
   * @var string
   */
  private $_root;
  
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->_root = $this->_makeDirectory(0777);
    
    require_once "JooS/Stream/Entity.php";
    
    $content = JooS_Stream_Entity::newInstance($this->_root);
    
    parent::__construct($content);
  }

}
