<?php
	namespace Bolt;

	abstract class View extends Base
	{
		public function __toString()
		{
			return "Bolt Object: " . $this->className();
		}
	}
?>
