<?php
	namespace Bolt;

	class Crypto extends Base
	{
		protected string $vector;
		protected int $vectorSize;

		protected string $algorithm;
		protected string $key;

		protected bool $encoding;

		public function __construct($algorithm = "aes-128-cbc", $mode = null, $key = "1234567890ABCDEFabcdef1234567890", $base64 = true, $vector = null, $auto = true)
		{
			$this->algorithm($algorithm);
			$this->key($key);
			$this->vector($vector);
			$this->encoding($base64);

			if ($auto === true)
			{
				$this->open();
			}
		}

		public function __destruct()
		{
			$this->close();
		}

		public function open()
		{
			$this->setVector($this->vector);
		}

		public function close()
		{
		}

		public function encrypt($text)
		{
			$data = openssl_encrypt($text,  $this->algorithm, $this->key, OPENSSL_RAW_DATA, $this->vector);

			if ($this->vectorSize > 0)
			{
				$data = $this->vector . $data;
			}

			return ($this->encoding === true) ? base64_encode($data) : $data;
		}

		public function decrypt($data)
		{
			$data = ($this->encoding === true) ? base64_decode($data) : $data;

			if ($this->vectorSize > 0)
			{
				// set iv
				$this->setVector(substr($data, 0, ($this->vectorSize)));

				// remove iv
				$data = substr($data, $this->vectorSize);
			}

			return trim(openssl_decrypt($data,  $this->algorithm, $this->key, OPENSSL_RAW_DATA, $this->vector)); // trim removes padding added to the key when originally encrypted)
		}

		public function setVector($vector = null)
		{
			$this->vectorSize = openssl_cipher_iv_length($this->algorithm);
			$this->vector = ($vector === null) ? random_bytes($this->vectorSize) : $vector;

			return $this->vector();
		}
	}
?>
