<?php
	namespace Bolt\Connections;

	use Bolt\Base;
	use Bolt\Exceptions\Dbo as Exception;
	use Bolt\Interfaces\Connection;
	use PDO;
	use PDOException;
	use PDOStatement;

	class Dbo extends Base implements Connection
	{
		protected ?PDO $connection = null;
		protected PDOStatement $statement;

		public Config\Dbo $config;

		public function __construct(Config\Dbo $config)
		{
			$this->config($config);

			if ($this->config->auto() === true)
			{
				$this->connect();
			}
		}

		public function __destruct()
		{
			$this->disconnect();
		}

		public function type()
		{
			return strtolower($this->className(false));
		}

		public function state(): string
		{
			return ($this->connection == "") ? "Disconnected" : "Connected";
		}

		public function connect(): self
		{
			$options = array();

			switch ($this->config->type())
			{
				case "mysql":
					$dsn = $this->config->type() . ":host=" . $this->config->host() . ";port=" . $this->config->port() . ";dbname=" . $this->config->database() . ";charset=utf8";
					$options = array(
						PDO::ATTR_EMULATE_PREPARES => false,
						PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
					);
					break;
				case "sqlite":
					$dsn = $this->config->type() . ":" . $this->config->database();
					break;
			}

			try
			{
				$this->connection(new PDO($dsn, $this->config->username(), $this->config->password(), $options));
			}
			catch (PDOException $error)
			{
				throw new Exception($error);
			}

			return $this;
		}

		public function disconnect(): self
		{
			$this->connection(null);

			return $this;
		}

		public function prepare(string $SQL): self
		{
			try
			{
				$this->statement = $this->connection->prepare($SQL);
			}
			catch (PDOException $error)
			{
				throw new Exception($error);
			}

			return $this;
		}

		public function bind(array $values): self
		{
			$arrayType = ($values == array_values($values)) ? "NUM" : "ASSOC";

			if (count($values) > 0)
			{
				foreach ($values as $key => $value)
				{
					$paramType = $this->getParameterType($value);

					$id = $key;

					if ($arrayType != "ASSOC")
					{
						$id = $key + 1;
					}

					try
					{
						$this->statement->bindParam($id, $values[$key], $paramType);
					}
					catch (PDOException $error)
					{
						throw new Exception($error);
					}
				}
			}

			return $this;
		}

		public function execute(): self
		{
			try
			{
				$this->statement->execute();
			}
			catch (PDOException $error)
			{
				throw new Exception($error);
			}

			return $this;
		}

		public function fetch(string $SQL, bool $return = false, bool $single = true, int $style = PDO::FETCH_ASSOC, $argument = null)
		{
			$results = array();
			$queryType = strtoupper(substr($SQL, 0, strpos($SQL, " ")));

			if ($queryType == "SELECT" || $queryType == "SHOW")
			{
				switch ($style)
				{
					case PDO::FETCH_CLASS:
					case PDO::FETCH_COLUMN:
					case PDO::FETCH_FUNC:
						$results = $this->statement->fetchAll($style, $argument);
						break;
					default:
						$results = $this->statement->fetchAll($style);
						break;
				}

				if (count($results) == 0)
				{
					$results = false;
				}
				elseif ($return === true && $single === true)
				{
					$results = $results[0];
				}
			}
			elseif ($queryType == "INSERT" && $return === true && $single === true)
			{
				$id = $this->connection->lastInsertId();

				$cleaned = str_replace("\n", " ", $SQL); // Todo: use better system of determining table name
				$table = substr($cleaned, 12, strpos($cleaned, " ", 12) - 12);
				$index = $this->query("SHOW INDEX FROM `" . $this->config->database() . "`." . $table . " WHERE `Key_name` = 'PRIMARY'", array(), true); // possible security issue here as the statement ?cant? be prepared
				$key = $index['Column_name'];

				$SQL = "SELECT * FROM " . $table . " WHERE " . $key . " = " . $id;
				$results = $this->query($SQL, array(), true, $style, $argument);
			}
			elseif (in_array($queryType, array("UPDATE", "DELETE")) && $return === true)
			{
				$results = array(
					"success" => ($results === false) ? false : true,
					"affected" => $this->statement->rowCount()
				);
			}

			return $results;
		}

		public function query(string $SQL, array $parameters = array(), bool $return = false, int $style = PDO::FETCH_ASSOC, $argument = null)
		{
			if ($this->connection == "")
			{
				throw new Exception("Not connected");
			}

			$this->prepare($SQL);

			if (!is_array(reset($parameters)))
			{
				$parameters = array($parameters);
				$single = true;
			}
			else
			{
				$single = false;
			}

			for ($loop = 0; $loop < count($parameters); $loop++)
			{
				$values = $parameters[$loop];

				$this->bind($values);
				$this->execute();

				$results[] = $this->fetch($SQL, $return, $single, $style, $argument);
			}

			if ($single === true)
			{
				$results = $results[0];
			}

			return $results;
		}

		private function getParameterType($value): int
		{
			if ($value === true || $value === false)
			{
				$type = PDO::PARAM_BOOL;
			}
			elseif ($value === null)
			{
				$type = PDO::PARAM_NULL;
			}
			elseif (is_int($value))
			{
				$type = PDO::PARAM_INT;
			}
			else
			{
				$type = PDO::PARAM_STR;
			}

			return $type;
		}

		public function interpolate(string $SQL, array $parameters): string
		{
			$keys = array();

			foreach ($parameters as $key => $value)
			{
				$keys[] = is_string($key) ? "/" . $key . "/" : "/[?]/";

				if (is_int($value))
				{
					$parameters[$key] = $value;
				}
				elseif (is_bool($value))
				{
					$parameters[$key] = ($value) ? "TRUE" : "FALSE";
				}
				elseif ($value === null)
				{
					$parameters[$key] = "NULL";
				}
				else
				{
					$parameters[$key] = "'" . $value . "'";
				}
			}

			$query = preg_replace($keys, $parameters, $SQL, 1);

			return $query;
		}

		public function transactionStart(): self
		{
			$this->connection->beginTransaction();

			return $this;
		}

		public function transactionEnd(): self
		{
			$this->connection->commit();

			return $this;
		}
	}
?>
