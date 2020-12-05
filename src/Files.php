<?php
	namespace Bolt;

	use Bolt\Exceptions\Codes\Files as Codes;
	use Bolt\Exceptions\Files as Exception;

	class Files
	{
		public $resource = null;
		public array $stats;

		public function open(string $filename, string $mode = "w", int $permissions = 0777): self
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
					throw new Exception(Codes::FILE_DOES_NOT_EXIST);
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

			return $this;
		}

		public function close(): self
		{
			if ($this->resource === null)
			{
				throw new Exception(Codes::FILE_NOT_OPEN);
			}

			if (fclose($this->resource) === true)
			{
				$this->resource = false;
				$this->stats = [];
			}

			return $this;
		}

		public function write(string $content): self
		{
			$result = fwrite($this->resource, $content);

			if ($result === false)
			{
				throw new Exception(Codes::ERROR_WRITING_TO_FILE);
			}

			return $this;
		}

		public function read(int $length = 0): string
		{
			if ($this->resource === null)
			{
				throw new Exception(Codes::FILE_NOT_OPEN);
			}

			if ($length == null)
			{
				$length = $this->stats['size'];
			}

			$content = fread($this->resource, $length);

			if ($content === false)
			{
				throw new Exception(Codes::ERROR_READING_FROM_FILE);
			}

			return $content;
		}

		public function seek(int $position, int $type = SEEK_SET): bool
		{
			if ($this->resource === null)
			{
				throw new Exception(Codes::FILE_NOT_OPEN);
			}

			return fseek($this->resource, $position, $type) === 0;
		}

		public function create(string $filename, string $content, int $permissions = 0777): self
		{
			if ($this->resource !== null)
			{
				throw new Exception(Codes::FILE_ALREADY_OPEN);
			}

			$this
				->open($filename, "w+", $permissions)
				->write($content)
				->close();

			return $this;
		}

		public function load(string $filename): string
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
