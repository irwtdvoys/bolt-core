<?php
	namespace Bolt\Curl;

	use Bolt\Http;

	class Response extends Http
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
