<?php

require_once "JooS/Stream/Wrapper/FS.php";

class JooS_Stream_Wrapper_FS_Partition_VirtualTest extends PHPUnit_Framework_TestCase
{
  
  public function testInstance()
  {
    JooS_Stream_Wrapper_FS::register("phpunit-testing", null);
    
    $d = dir("phpunit-testing://");
    $this->assertFalse($d->read());
    $d->close();

    JooS_Stream_Wrapper_FS::unregister("phpunit-testing");
  }

}
