<?php

require_once "JooS/Stream/Wrapper.php";

class JooS_Stream_WrapperTest extends PHPUnit_Framework_TestCase
{
  protected $protocol = null;

  public function testRegister()
  {
    $exceptionsCounter = 0;
    
    $result2 = JooS_Stream_Wrapper::unregister($this->protocol);
    $this->assertTrue($result2);
    
    try {
      JooS_Stream_Wrapper::unregister($this->protocol);
    }
    catch (JooS_Stream_Wrapper_Exception $e) {
      $exceptionsCounter++;
    }
    $this->assertEquals(1, $exceptionsCounter, "Wrapper must not be registered already");
    
    $result1 = JooS_Stream_Wrapper::register($this->protocol);
    $this->assertTrue($result1);
    
    try {
      JooS_Stream_Wrapper::register($this->protocol);
    }
    catch (JooS_Stream_Wrapper_Exception $e) {
      $exceptionsCounter++;
    }
    $this->assertEquals(2, $exceptionsCounter, "Second wrapper could not be registered");
    
    $this->assertTrue(
      in_array($this->protocol, stream_get_wrappers())
    );
  }
  
  protected function setUp()
  {
    if (is_null($this->protocol)) {
      $this->protocol = uniqid("stream");
    }

    JooS_Stream_Wrapper::register($this->protocol);
  }
  
  protected function tearDown()
  {
    JooS_Stream_Wrapper::unregister($this->protocol);
  }

}
