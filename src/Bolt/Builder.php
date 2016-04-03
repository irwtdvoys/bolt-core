<?php
	namespace Bolt;

	class Builder
	{
		public static function connection($name, $config)
		{
			$configName = str_replace("Connections\\", "Connections\\Config\\", $name);

			$class = new $name(new $configName($config));

			return $class;
		}

		public static function model($name, Interfaces\Connection $connection, $data = null)
		{
			return new $name($connection, $data);
		}

		public static function core($name)
		{
			return new $name();
		}
	}
?>
