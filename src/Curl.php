<?php
	namespace Bolt;

	use Bolt\Exceptions\Curl as Exception;

	class Curl extends Http
	{
		private $resource = null;
		public $options = array();

		public $info;
		public $data;

		public function open($options = null)
		{
			$this->resource = curl_init();

			if (is_array($options) === true)
			{
				foreach ($options as $option => $value)
				{
					$this->set($option, $value);
				}
			}
		}

		public function close()
		{
			curl_close($this->resource);
			$this->resource = false;
			$this->info = null;
			$this->data = null;
		}

		public function execute()
		{
			$this->data = curl_exec($this->resource);
			$this->info = curl_getinfo($this->resource);

			if ($this->info['http_code'] === 0)
			{
				throw new Exception("Error executing cURL request", $this->error());
			}
		}

		public function set($option, $value)
		{
			$this->options[$option] = $value;

			return curl_setopt($this->resource, $option, $value);
		}

		public function get($option)
		{
			return $this->options[$option];
		}

		public function fetch($options = null)
		{
			$this->open($options);
			$this->execute();
			$result = new Curl\Response($this->info['http_code'], $this->parseBody($this->data, $this->info['content_type']));
			$this->close();

			return $result;
		}

		public function error()
		{
			return curl_errno($this->resource);
		}

		private function parseBody($body, $contentType)
		{
			switch ($contentType)
			{
				case "application/json":
				case "application/json; charset=UTF-8":
				case "application/json; charset=utf-8":
					$parsed = json_decode($body);
					break;
				default:
					$parsed = $body;
					break;
			}

			return $parsed;
		}
	}
?>
