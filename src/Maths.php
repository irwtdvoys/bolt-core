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

		public static function median($numbers)
		{
			asort($numbers);

			$data = (array_values($numbers));
			$point = (count($data) - 1) / 2;

			if (is_integer($point))
			{
				return $data[$point];
			}

			$points = array(
				$data[(integer)floor($point)],
				$data[(integer)ceil($point)]
			);

			return self::mean($points);
		}
	}
?>
