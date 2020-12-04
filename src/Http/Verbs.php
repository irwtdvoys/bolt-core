<?php
	namespace Bolt\Http;

	use Bolt\Enum;

	class Verbs extends Enum
	{
		const GET = "GET";
		const HEAD = "HEAD";
		const POST = "POST";
		const PUT = "PUT";
		const DELETE = "DELETE";
		const CONNECT = "CONNECT";
		const OPTIONS = "OPTIONS";
		const TRACE = "TRACE";
		const PATCH = "PATCH";

		public static function list(): array
		{
			return array_values(parent::expose());
		}
	}
?>
