<?php
	namespace Bolt;

	class Arrays
	{
		public static function removeElement($needle, $haystack)
		{
			return array_values(array_diff($haystack, array($needle)));
		}

		public static function reKey($array)
		{
			$results = array();

			foreach ($array as $element)
			{
				$results[] = $element;
			}

			return $results;
		}

		public static function subValueSort($array, $subkey, $order = "ASC")
		{
			foreach($array as $key => $value)
			{
				$subArray[$key] = strtolower($value[$subkey]);
			}

			if ($order == "ASC")
			{
				asort($subArray);
			}
			else
			{
				arsort($subArray);
			}

			foreach ($subArray as $key => $val)
			{
				$results[] = $array[$key];
			}

			return $results;
		}

		public static function type($array)
		{
			if (!is_array($array))
			{
				$result = false;
			}
			elseif (array_values($array) === $array)
			{
				$result = "numeric";
			}
			else
			{
				$result = "assoc";
			}

			return $result;
		}

		public static function check($keys, $array)
		{
			foreach ($keys as $key => $value)
			{
				if (Arrays::type($keys) === "numeric" || is_integer($key))
				{
					$field = $value;
					$data = null;
				}
				else
				{
					$field = $key;
					$data = $value;
				}

				if (!is_array($array) && $array[$keys] === null)
				{
					return $field;
				}

				if ($data !== null)
				{
					$check = self::check($data, $array[$field]);

					if ($check !== true)
					{
						return $field . "." . $check;
					}
				}
			}

			return true;
		}

		public static function filter($keys, $array)
		{
			$result = array();

			foreach ($keys as $key => $value)
			{
				if (Arrays::type($keys) === "numeric" || is_integer($key))
				{
					$field = $value;
					$data = null;
				}
				else
				{
					$field = $key;
					$data = $value;
				}

				if (isset($array[$field]))
				{
					$result[$field] = ($data === null) ? $array[$field] : self::filter($keys[$field], $array[$field]);
				}
			}

			return $result;
		}
	}
?>
