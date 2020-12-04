<?php
	namespace Bolt;

	class Dsv
	{
		public $delimiter;
		public $enclosure;

		public $headers = array();
		public $data = array();

		public $stream;

		protected $files;

		public function __construct(string $delimiter = ",", string $enclosure = '"', array $data = null)
		{
			$this->delimiter = $delimiter;
			$this->enclosure = $enclosure;

			if ($data !== null)
			{
				$this->addData($data);
			}

			$this->files = new Files();
		}

		public function __destructor()
		{
			fclose($this->stream);
		}

		public function addData($records)
		{
			$this->addHeaders(array_keys(reset($records)));
			$this->addContent($records);
		}

		public function addHeaders($record)
		{
			$this->headers = $record;
		}

		public function addContent($records)
		{
			foreach ($records as $record)
			{
				$this->data[] = $record;
			}
		}

		public function addRow($record)
		{
			$this->data[] = $record;
		}

		public function writeRow($record)
		{
			fputcsv($this->stream, $record, $this->delimiter, $this->enclosure);
		}

		public function generate()
		{
			$this->writeRow($this->headers);

			foreach ($this->data as $record)
			{
				$this->writeRow($record);
			}
		}

		public function load($filename)
		{
			$this->files->open($filename, "r");

			$count = 0;

			while (($row = fgetcsv($this->files->resource, 0, $this->delimiter, $this->enclosure)) !== false)
			{
				if ($count === 0)
				{
					$this->addHeaders($row);
				}
				else
				{
					$this->addRow($row);
				}

				$count++;
			}

			$this->files->close();
		}

		public function save($filename = "temp.csv")
		{
			$this->stream = fopen(ROOT_SERVER . "files/" . $filename, "x");
			$this->generate();
			fclose($this->stream);
		}

		public function output(string $filename = "export.csv"): void
		{
			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=" . $filename);

			$this->stream = fopen("php://output", "w");
			$this->generate();
			fclose($this->stream);
		}

		public function toStructure()
		{
			$results = array();

			foreach ($this->data as $next)
			{
				$record = array();

				foreach ($next as $key => $value)
				{
					$record[$this->headers[$key]] = $value;
				}

				$results[] = $record;
			}

			return $results;
		}
	}
?>
