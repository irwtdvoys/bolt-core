<?php
	namespace Bolt\Exceptions\Codes;

	use \Bolt\Codes;

	class Files extends Codes
	{
		const FILE_NOT_OPEN = 1;
		const FILE_ALREADY_OPEN = 2;
		const FILE_DOES_NOT_EXIST = 3;

		const ERROR_WRITING_TO_FILE = 4;
		const ERROR_READING_FROM_FILE = 5;
	}
?>
