<?php
	namespace Bolt;

	use \Bolt\Exceptions\Framework as Exception;

	abstract class Base
	{
		public function __construct($data = null)
		{
			if ($data !== null)
			{
				$this->populate($data);
			}
		}

		protected function populate($data)
		{
			$properties = $this->getProperties();

			if (count($properties) > 0)
			{
				foreach ($properties as $property)
				{
					if (is_array($data))
					{
						$value = isset($data[$property->name]) ? $data[$property->name] : null;
					}
					else
					{
						$value = isset($data->{$property->name}) ? $data->{$property->name} : null;
					}

					if ($value !== null)
					{
						$this->{$property->name}($value);
					}
				}
			}

			return true;
		}

		protected function getProperties()
		{
			$reflection = new \ReflectionClass($this->className());
			return $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
		}

		public function className($full = true)
		{
			$className = get_class($this);

			if ($full === false)
			{
				$namespace = explode("\\", $className);
				$className = $namespace[count($namespace) - 1];
			}

			return $className;
		}

		protected function calculateNamespace($object)
		{
			$namespace = array(
				__NAMESPACE__,
				$this->className(),
				ucwords($object->class)
			);

			$namespace = array_values(array_filter($namespace));
			$className = implode("\\", $namespace);

			return $className;
		}

		public function __call($name, $args)
		{
			$class = $this->className();

			if (property_exists($class, $name) === false)
			{
				throw new Exception("Property `" . $name . "` not found on class `" . $class . "`", 1);
			}

			if ($args == array())
			{
				return $this->$name;
			}

			$this->$name = $args[0];

			return true;
		}
	}
?>
