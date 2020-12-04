<?php
	declare(strict_types=1);

	namespace Bolt;

	use Bolt\Exceptions\Json as Exception;

	class Json
	{
		public $data = null;

		public function __construct($data = null)
		{
			$this->data = $data;

			if (is_string($data))
			{
				$this->data = $this->decode($data);
			}
		}

		public function toString(): string
		{
			return self::encode($this->data);
		}

		public static function encode($data, int $flags = 0, int $depth = 512): string
		{
			$result = json_encode($data, $flags, $depth);

			$error = json_last_error();

			if ($error !== JSON_ERROR_NONE)
			{
				throw new Exception("Error encoding JSON", $error);
			}

			return $result;
		}

		public static function decode($data)
		{
			$result = json_decode($data);

			$error = json_last_error();

			if ($error !== JSON_ERROR_NONE)
			{
				throw new Exception("Error decoding JSON", $error);
			}

			return $result;
		}

		public static function validate($data): bool
		{
			try
			{
				self::decode($data);
			}
			catch (Exception $exception)
			{
				return false;
			}

			return true;
		}
	}
?>
