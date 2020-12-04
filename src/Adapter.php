<?php
	namespace Bolt;

	use Bolt\Interfaces\Connection;

	abstract class Adapter
	{
		protected Connection $resource;
	}
?>
