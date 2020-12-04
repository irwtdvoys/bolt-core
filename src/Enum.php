<?php
	namespace Bolt;

	use ReflectionClass;

	abstract class Enum
	{
		public static function expose(): array
		{
			return self::identifiers();
		}

		protected static function identifiers(): array
		{
			$refl = new ReflectionClass(get_called_class());
			$constants = $refl->getConstants();
			asort($constants);

			return $constants;
		}
	}
?>
