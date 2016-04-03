<?php
	namespace Bolt\Exceptions;

	use \Exception;

	class Files extends Exception
	{
		protected $codes;

		public function __construct($code, Exception $previous = null)
		{
			$codes = new Codes\Files();

			parent::__construct($codes->fromCode($code), $code, $previous);
		}
	}
?>
