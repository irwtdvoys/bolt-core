<?php
	namespace Bolt\Exceptions;

	use \Exception;

	class Framework extends Exception
	{
		protected $codes;

		public function __construct($message = null, $code = 0, Exception $previous = null)
		{
			$codes = new Codes\Framework();

			parent::__construct($codes->fromCode($code), $code, $previous);
		}
	}
?>
