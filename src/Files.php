<?php
	namespace Bolt;

	use Bolt\Exceptions\Codes\Files as Codes;
	use Bolt\Exceptions\Files as Exception;

	class Files
	{
		public $resource = null;
		public $stats;

		public function open($filename, $mode = "w", $permissions = 0777)
		{
			$created = false;

			if ($this->resource !== null)
			{
				throw new Exception(Codes::FILE_ALREADY_OPEN);
			}

			if (file_exists($filename) === false)
			{
				if ($mode == "r" || $mode == "r+")
				{
					return false;
				}
				else
				{
					$created = true;
					$directory = dirname($filename);

					if (is_dir($directory) === false)
					{
						mkdir($directory, $permissions, true);
						chmod($directory, $permissions);
					}
				}
			}

			$this->resource = fopen($filename, $mode);

			if ($this->resource !== null)
			{
				if ($created === true)
				{
					chmod($filename, $permissions);
				}

				$this->stats = fstat($this->resource);
			}

			return true;
		}

		public function close()
		{
			if ($this->resource === null)
			{
				throw new Exception(Codes::FILE_NOT_OPEN);
			}

			if (fclose($this->resource) === true)
			{
				$this->resource = false;
				$this->stats = null;
			}

			return true;
		}

		public function write($content)
		{
			if (fwrite($this->resource, $content) === false)
			{
				return false;
			}
		}

		public function read($length = null)
		{
			if ($this->resource === null)
			{
				throw new Exception(Codes::FILE_NOT_OPEN);
			}

			if ($length == null)
			{
				$length = $this->stats['size'];
			}

			return fread($this->resource, $length);
		}

		public function seek($position, $type = SEEK_SET)
		{
			if ($this->resource === null)
			{
				throw new Exception(Codes::FILE_NOT_OPEN);
			}

			return (fseek($this->resource, $position, $type) == 0) ? true : false;
		}

		public function create($filename, $content, $permissions = 0777)
		{
			if ($this->resource !== null)
			{
				throw new Exception(Codes::FILE_ALREADY_OPEN);
			}

			$this->open($filename, "w+", $permissions);
			$this->write($content);
			$this->close();

			return true;
		}

		public function load($filename)
		{
			if ($this->resource !== null)
			{
				throw new Exception(Codes::FILE_ALREADY_OPEN);
			}

			$this->open($filename, "r");
			$content = $this->read();
			$this->close();

			return $content;
		}
	}
?>
