<?php
	namespace Bolt\Exceptions\Codes;

	use \Bolt\Codes;

	class Json extends Codes
	{
		const JSON_ERROR_NONE = 0;
		const JSON_ERROR_DEPTH = 1;
		const JSON_ERROR_STATE_MISMATCH = 2;
		const JSON_ERROR_CTRL_CHAR = 3;
		const JSON_ERROR_SYNTAX = 4;
		const JSON_ERROR_UTF8 = 5;
		const JSON_ERROR_RECURSION = 6;
		const JSON_ERROR_INF_OR_NAN = 7;
		const JSON_ERROR_UNSUPPORTED_TYPE = 8;
		const JSON_ERROR_INVALID_PROPERTY_NAME = 9;
		const JSON_ERROR_UTF16 = 10;
	}
?>
