<?php
	namespace Bolt;

	class Maths
	{
		public static function _double($value, $iterations = 1)
		{
			$result = $value;

			if ($iterations > 0)
			{
				$result = self::_double($result * 2, ($iterations - 1));
			}

			return $result;
		}

		public static function average($numbers)
		{
			return self::mean($numbers);
		}

		public static function mean($numbers)
		{
			$count = 0;
			$total = 0;

			foreach ($numbers as $next)
			{
				if (is_numeric($next))
				{
					$total += $next;
					$count++;
				}
			}

			return $total / $count;
		}
	}
?>
