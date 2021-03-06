<?php
	namespace Bolt;

	use Bolt\Curl\Response;
	use Bolt\Exceptions\Curl as Exception;

	class Curl extends Base
	{
		private $resource = null;
		public array $options = array();

		public ?array $info = null;
		public ?string $data = null;

		public function open(array $options): self
		{
			$this->resource = curl_init();
			$this->options = array(); // reset options store

			$this->set(CURLOPT_HEADER, true); // default headers to on

			foreach ($options as $option => $value)
			{
				$this->set($option, $value);
			}

			return $this;
		}

		public function close(): self
		{
			curl_close($this->resource);
			$this->resource = false;
			$this->info = null;
			$this->data = null;

			return $this;
		}

		public function execute(): self
		{
			$this->data = curl_exec($this->resource);
			$this->info = curl_getinfo($this->resource);

			if ($this->info['http_code'] === 0)
			{
				throw new Exception("Error executing cURL request", $this->error());
			}

			return $this;
		}

		public function set(int $option, $value): bool
		{
			$this->options[$option] = $value;

			return curl_setopt($this->resource, $option, $value);
		}

		public function get(int $option)
		{
			return $this->options[$option];
		}

		public function fetch(array $options): Response
		{
			$this->open($options);
			$this->execute();

			$body = $this->data;
			$parsed = null;

			if ($this->get(CURLOPT_HEADER) === true)
			{
				// Headers regex
				$pattern = "/^HTTP\/(?'version'\d\.?\d?)\s(?'code'\d{3})\s?(?'message'[\w ]*)\X*\R\R/ims";

				// Extract headers from response
				preg_match($pattern, $this->data, $matches);

				$headersString = $matches[0];

				if (!isset($headersString) || empty($headersString))
				{
					throw new Exception("Error parsing response headers");
				}

				$headers = explode(PHP_EOL, trim($headersString));

				// Remove headers from the response body
				$body = str_replace($headersString, '', $this->data);

				// Extract the version and status from the first header
				array_shift($headers);
				$parsed['http-version'] = $matches['version'];
				$parsed['status-code'] = $matches['code'];
				$parsed['status'] = $matches['code'] . ' ' . $matches['message'];

				// Convert headers into an associative array
				foreach ($headers as $header)
				{
					preg_match("/(?'key'.*?)\:\s(?'value'[\S ]+)/i", $header, $matches);
					$parsed[strtolower($matches['key'])] = $matches['value'];
				}
			}

			$result = new Response($this->info['http_code'], $this->parseBody($body, $this->info['content_type']), $parsed);
			$this->close();

			return $result;
		}

		public function error(): int
		{
			return curl_errno($this->resource);
		}

		private function parseBody(string $body, string $contentType)
		{
			switch ($contentType)
			{
				case "application/json":
				case "application/json; charset=UTF-8":
				case "application/json; charset=utf-8":
					$parsed = Json::decode($body);
					break;
				default:
					$parsed = $body;
					break;
			}

			return $parsed;
		}
	}
?>
