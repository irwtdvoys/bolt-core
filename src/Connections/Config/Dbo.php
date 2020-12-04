<?php
	namespace Bolt\Connections\Config;

	use Bolt\Base;

	class Dbo extends Base
	{
		public string $type;
		public string $host;
		public int $port;
		public string $database;
		public string $username;
		public string $password;
		public bool $auto;

		public function __construct($data = null)
		{
			parent::__construct($data);

			$this->auto((bool)$this->auto());
		}
	}
?>
