<?php
	namespace Bolt\Curl;

	class Response extends \Bolt\Http
	{
		public $code;
		public $status;
		public $headers;
		public $body;

		public function __construct($code, $body, $headers = null)
		{
			$this->code = $code;
			$this->status = $this->codeLookup($code);
			$this->body = $body;
			$this->headers = $headers;
		}
	}
?>
