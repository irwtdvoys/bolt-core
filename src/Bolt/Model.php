<?php
	namespace Bolt;

	use Bolt\Interfaces\Connection;

	abstract class Model extends Base
	{
		protected $interface;

		public function __construct(Connection $connection, $data = null)
		{
			$className = $this->classname();

			$className = "Bolt\\Interfaces\\Models\\" . $className . "\\" . $connection->classname(false);

			$this->interface = new $className($connection);

			if ($data !== null)
			{
				$this->populate($data);
			}
		}

		public function toJson()
		{
			$results = null;
			$properties = $this->getProperties();

			foreach ($properties as $property)
			{
				$value = $this->{$property->name};

				#if ($value !== null)
				{
					if (is_array($value))
					{
						foreach ($value as &$element)
						{
							if (is_object($element) && get_class($element) != "stdClass")
							{
								$element = json_decode($element->toJson());
							}
						}

						if ($value === array())
						{
							$value = null;
						}
					}

					if (is_object($value))
					{
						if (get_class($value) != "stdClass")
						{
							$value = json_decode($value->toJson());
						}
					}

					#if ($value !== null)
					{
						$results[$property->name] = $value;
					}
				}
			}

			return json_encode($results);
		}

		public function load()
		{
			$this->populate($this->interface->load());
		}
	}
?>
