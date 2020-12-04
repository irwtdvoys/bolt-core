<?php
	namespace Bolt;

	use Bolt\Exceptions\Curl as Exception;

	class Curl extends Base
	{
		private $resource = null;
		public $options = array();

		public $info;
		public $data;

		public function open($options = null)
		{
			$this->resource = curl_init();
			$this->options = array(); // reset options store

			$this->set(CURLOPT_HEADER, true); // default headers to on

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

			$body = $this->data;
			$parsed = null;

			if ($this->get(CURLOPT_HEADER) === true)
			{
				# Headers regex
				$pattern = "/^HTTP\/(?'version'\d\.?\d?)\s(?'code'\d{3})\s?(?'message'[\w ]*)\X*\R\R/ims";

				# Extract headers from response
				preg_match($pattern, $this->data, $matches);

				$headersString = $matches[0];

				if (!isset($headersString) || empty($headersString))
				{
					throw new Exception("Error parsing response headers");
				}

				$headers = explode(PHP_EOL, trim($headersString));

				# Remove headers from the response body
				$body = str_replace($headersString, '', $this->data);

				# Extract the version and status from the first header
				array_shift($headers);
				$parsed['http-version'] = $matches['version'];
				$parsed['status-code'] = $matches['code'];
				$parsed['status'] = $matches['code'] . ' ' . $matches['message'];

				# Convert headers into an associative array
				foreach ($headers as $header)
				{
					preg_match("/(?'key'.*?)\:\s(?'value'[\w ]*)/", $header, $matches);
					$parsed[strtolower($matches['key'])] = $matches['value'];
				}
			}

			$result = new Curl\Response($this->info['http_code'], $this->parseBody($body, $this->info['content_type']), $parsed);
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
