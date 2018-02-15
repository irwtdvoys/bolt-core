<?php
	namespace Bolt;

	abstract class Exception extends \Exception
	{
		protected $codes;

		public function __construct($message = null, $code = 0, Exception $previous = null)
		{
			$parts = explode("\\", get_class($this));

			$type = array_pop($parts);
			$parts[] = "Codes";
			$parts[] = $type;
			$codesClass = implode("\\", $parts);

			$this->codes = new $codesClass();

			if ($message === null)
			{
				$message = $this->codes->fromCode($code);
			}

			parent::__construct($message, $code, $previous);
		}

		public function getCodeKey()
		{
			return $this->codes->fromCode($this->getCode());
		}
	}
?>
