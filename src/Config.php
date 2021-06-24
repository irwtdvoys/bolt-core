<?php
	namespace Bolt;

	class Config extends Base
	{
		private array $constants;

		public function __construct()
		{
			$this->constants = get_defined_constants(true);

			if (isset($this->constants['user']))
			{
				ksort($this->constants['user']);
			}
		}

		public function getByName($constant)
		{
			return $this->constants['user'][$constant];
		}

		public function getByPrefix($prefix)
		{
			$results = array();

			if (count($this->constants['user']) > 0)
			{
				foreach ($this->constants['user'] as $key => $value)
				{
					if (strpos($key, $prefix) === 0)
					{
						$results[$key] = $value;
					}
				}
			}

			return $results;
		}

		public function inflate($data, $limit = false, $depth = 0)
		{
			$results = array();

			if ($data !== null && count($data) > 0)
			{
				foreach ($data as $key => $value)
				{
					list($head, $tail) = array_pad(explode("_", strtolower($key), 2), 2, null);

					if ($tail == "")
					{
						$results[$key] = $value;
					}
					else
					{
						$results[$head][$tail] = $value;
					}
				}

				if ($limit === false || $depth < $limit)
				{
					foreach ($results as &$next)
					{
						if ($next !== null && !is_scalar($next) && count($next) > 1)
						{
							$next = $this->inflate($next, $limit, ($depth + 1));
						}
					}
				}
			}

			return $results;
		}

		public function info($type = null)
		{
			if ($type === null)
			{
				$result = $this->inflate($this->constants['user']);
			}
			else
			{
				$info = $this->inflate($this->getByPrefix(strtoupper($type) . "_"));
				$result = $info[$type] ?? (object)[];
			}

			return $result;
		}
	}
?>
