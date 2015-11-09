<?php
	namespace Bolt;

	class Crypto extends Base
	{
		private $mod = null; // Resource
		protected $vector;
		protected $vectorSize;

		protected $algorithm;
		protected $mode;
		protected $key;

		protected $encoding;

		public function __construct($algorithm = "tripledes", $mode = "ecb", $key = "ABCDEFGH12345678abcdefgh", $base64 = true, $vector = null, $auto = true)
		{
			if ($this->cryptographyCheck() === false)
			{
				throw new \Exception("Mcrypt not available on server");
			}

			$this->algorithm($algorithm);
			$this->mode($mode);
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

		public function cryptographyCheck()
		{
			return function_exists("mcrypt_encrypt") ? true : false;
		}

		public function open()
		{
			$this->mod = mcrypt_module_open($this->algorithm, "", $this->mode, "");
			$this->setVector($this->vector);
			mcrypt_generic_init($this->mod, $this->key, $this->vector);
		}

		public function close()
		{
			mcrypt_generic_deinit($this->mod);
			mcrypt_module_close($this->mod);
			$this->mod = null;
		}

		public function encrypt($text)
		{
			$data = mcrypt_generic($this->mod, $text);

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
				$this->vector = substr($data, 0, ($this->vectorSize));
				$this->close();
				$this->open();

				// remove iv
				$data = substr($data, $this->vectorSize);
			}

			return trim(mdecrypt_generic($this->mod, $data)); // trim removes padding added to the key when originally encrypted)
		}

		public function setVector($vector = null)
		{
			$this->vector = ($vector === null) ? mcrypt_create_iv(mcrypt_enc_get_iv_size($this->mod), MCRYPT_RAND) : $vector;

			$this->vectorSize = mcrypt_enc_get_iv_size($this->mod);
			return $this->vector();
		}
	}
?>
