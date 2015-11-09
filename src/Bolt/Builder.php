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
	}
?>
