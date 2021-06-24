<?php
	namespace Bolt\Connections\Config;

	use Bolt\Base;

	class Dbo extends Base
	{
		public ?string $type = null;
		public ?string $host = null;
		public ?int $port = null;
		public ?string $database = null;
		public ?string $username = null;
		public ?string $password = null;
		public ?bool $auto = null;

		public function __construct($data = null)
		{
			parent::__construct($data);

			$this->auto((bool)$this->auto());
		}
	}
?>
