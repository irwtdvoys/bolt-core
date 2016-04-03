<?php
	class NachoTest extends PHPUnit_Framework_TestCase
	{
		public function testFirstOne()
		{
			$base = new Bolt\Stubs\Base();
			$this->assertInstanceOf("Bolt\Base", $base);
		}
	}
?>

