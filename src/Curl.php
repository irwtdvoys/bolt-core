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

			if ($this->get(CURLOPT_HEADER) == true)
			{
				# Headers regex
				$pattern = "#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims";

				# Extract headers from response
				preg_match_all($pattern, $this->data, $matches);
				$headers_string = array_pop($matches[0]);
				$headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));

				# Remove headers from the response body
				$body = str_replace($headers_string, '', $this->data);

				# Extract the version and status from the first header
				$version_and_status = array_shift($headers);
				preg_match("#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#", $version_and_status, $matches);

				$parsed = array();

				$parsed['http-version'] = $matches[1];
				$parsed['status-code'] = $matches[2];
				$parsed['status'] = $matches[2] . ' ' . $matches[3];

				# Convert headers into an associative array
				foreach ($headers as $header)
				{
					preg_match('#(.*?)\:\s(.*)#', $header, $matches);
					$parsed[strtolower($matches[1])] = $matches[2];
				}
			}
			else
			{
				$body = $this->data;
				$parsed = null;
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
