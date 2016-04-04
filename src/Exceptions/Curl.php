<?php
	namespace Bolt\Exceptions;

	use \Exception;

	class Curl extends Exception
	{
		protected $codes;

		public function __construct($code, Exception $previous = null)
		{
			$codes = new Codes\Curl();

			parent::__construct($codes->fromCode($code), $code, $previous);
		}
	}
?>
