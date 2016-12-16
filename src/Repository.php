<?php
	namespace Bolt;

	use Bolt\Interfaces\Connection;

	abstract class Repository extends Base
	{
		protected $adapter;

		public function __construct(Connection $connection = null, $data = null)
		{
			$this->adapter($connection);

			if ($data !== null)
			{
				parent::__construct($data);
			}
		}

		public function adapter(Connection $connection = null)
		{
			if ($connection !== null)
			{
				$className = $this->className(false);
				$className = "App\\Adapters\\Repositories\\" . $className . "\\" . $connection->className(false);

				$this->adapter = new $className($connection, $this);

				return true;
			}

			return $this->adapter;
		}

		public function __call($name, $args)
		{
			return $this->adapter->{$name}();
		}
	}
?>
