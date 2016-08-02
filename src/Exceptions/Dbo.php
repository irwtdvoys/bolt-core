<?php
	namespace Bolt\Exceptions;

	class Dbo extends \Exception
	{
		protected $codes;

		public function __construct(\PDOException $previous = null)
		{
			if ($previous->errorInfo === null)
			{
				preg_match('/SQLSTATE\[(\w+)\] \[(\w+)\] (.*)/', $previous->getMessage(), $matches);
				array_shift($matches);
			}
			else
			{
				$matches = $previous->errorInfo;
			}

			$code = $matches[0];
			$message = sprintf("[%s] %s", $matches[1], $matches[2]);

			parent::__construct($message, hexdec($code), $previous);
		}
	}
?>
