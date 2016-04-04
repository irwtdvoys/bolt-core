<?php
	namespace Bolt\Exceptions;

	use \Exception;

	class Crypto extends Exception
	{
		protected $codes;

		public function __construct($code, Exception $previous = null)
		{
			$codes = new Codes\Crypto();

			parent::__construct($codes->fromCode($code), $code, $previous);
		}
	}
?>
