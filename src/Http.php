<?php
	namespace Bolt;

	use Bolt\Http\Codes;

	class Http extends Base
	{
		public function groupLookup($code)
		{
			if ($code < 200)
			{
				$group = "informational";
			}
			elseif ($code < 300)
			{
				$group = "success";
			}
			elseif ($code < 400)
			{
				$group = "redirection";
			}
			elseif ($code < 500)
			{
				$group = "client error";
			}
			elseif ($code < 600)
			{
				$group = "server error";
			}
			else
			{
				$group = "unknown";
			}

			return $group;
		}

		public function codeLookup($code)
		{
			switch ($code)
			{
				case Codes::CONTINUE:
					$result = "Continue";
					break;
				case Codes::SWITCHING_PROTOCOLS:
					$result = "Switching Protocols";
					break;
				case Codes::PROCESSING:
					$result = "Processing";
					break;

				case Codes::OK:
					$result = "OK";
					break;
				case Codes::CREATED:
					$result = "Created";
					break;
				case Codes::ACCEPTED:
					$result = "Accepted";
					break;
				case Codes::NON_AUTHORITATIVE_INFORMATION:
					$result = "Non-Authoritative Information";
					break;
				case Codes::NO_CONTENT:
					$result = "No Content";
					break;
				case Codes::RESET_CONTENT:
					$result = "Reset Content";
					break;
				case Codes::PARTIAL_CONTENT:
					$result = "Partial Content";
					break;

				case Codes::MULTIPLE_CHOICES:
					$result = "Multiple Choices";
					break;
				case Codes::MOVED_PERMANENTLY:
					$result = "Moved Permanently";
					break;
				case Codes::FOUND:
					$result = "Found";
					break;
				case Codes::SEE_OTHER:
					$result = "See Other";
					break;
				case Codes::NOT_MODIFIED:
					$result = "Not Modified";
					break;
				case Codes::USE_PROXY:
					$result = "Use Proxy";
					break;
				case Codes::SWITCH_PROXY:
					$result = "Switch Proxy";
					break;
				case Codes::TEMPORARY_REDIRECT:
					$result = "Temporary Redirect";
					break;
				case Codes::PERMANENT_REDIRECT:
					$result = "Permanent Redirect";
					break;

				case Codes::BAD_REQUEST:
					$result = "Bad Request";
					break;
				case Codes::UNAUTHORIZED:
					$result = "Unauthorized";
					break;
				case Codes::PAYMENT_REQUIRED:
					$result = "Payment Required";
					break;
				case Codes::FORBIDDEN:
					$result = "Forbidden";
					break;
				case Codes::NOT_FOUND:
					$result = "Not Found";
					break;
				case Codes::METHOD_NOT_ALLOWED:
					$result = "Method Not Allowed";
					break;
				case Codes::NOT_ACCEPTABLE:
					$result = "Not Acceptable";
					break;
				case Codes::PROXY_AUTHENTICATION_REQUIRED:
					$result = "Proxy Authentication Required";
					break;
				case Codes::REQUEST_TIMEOUT:
					$result = "Request Timeout";
					break;
				case Codes::CONFLICT:
					$result = "Conflict";
					break;
				case Codes::GONE:
					$result = "Gone";
					break;
				case Codes::LENGTH_REQUIRED:
					$result = "Length Required";
					break;
				case Codes::PRECONDITION_FAILED:
					$result = "Precondition Failed";
					break;
				case Codes::REQUEST_ENTITY_TOO_LARGE:
					$result = "Request Entity Too Large";
					break;
				case Codes::REQUEST_URI_TOO_LONG:
					$result = "Request-URI Too Long";
					break;
				case Codes::UNSUPPORTED_MEDIA_TYPE:
					$result = "Unsupported Media Type";
					break;
				case Codes::REQUESTED_RANGE_NOT_SATISFIABLE:
					$result = "Requested Range Not Satisfiable";
					break;
				case Codes::EXPECTATION_FAILED:
					$result = "Expectation Failed";
					break;
				case Codes::AUTHENTICATION_TIMEOUT:
					$result = "Authentication Timeout";
					break;
				case Codes::UNPROCESSABLE_ENTITY:
					$result = "Unprocessable Entity";
					break;
				case Codes::LOCKED:
					$result = "Locked";
					break;
				case Codes::UPGRADE_REQUIRED:
					$result = "Upgrade Required";
					break;
				case Codes::PRECONDITION_REQUIRED:
					$result = "Precondition Required";
					break;
				case Codes::TOO_MANY_REQUESTS:
					$result = "Too Many Requests";
					break;
				case Codes::REQUEST_HEADER_FIELDS_TOO_LARGE:
					$result = "Request Header Fields Too Large";
					break;

				case Codes::INTERNAL_SERVER_ERROR:
					$result = "Internal Server Error";
					break;
				case Codes::NOT_IMPLEMENTED:
					$result = "Not Implemented";
					break;
				case Codes::BAD_GATEWAY:
					$result = "Bad Gateway";
					break;
				case Codes::SERVICE_UNAVAILABLE:
					$result = "Service Unavailable";
					break;
				case Codes::GATEWAY_TIMEOUT:
					$result = "Gateway Timeout";
					break;
				case Codes::HTTP_VERSION_NOT_SUPPORTED:
					$result = "HTTP Version Not Supported";
					break;
				case Codes::VARIANT_ALSO_NEGOTIATES:
					$result = "Variant Also Negotiates";
					break;
				case Codes::INSUFFICIENT_STORAGE:
					$result = "Insufficient Storage";
					break;
				case Codes::LOOP_DETECTED:
					$result = "Loop Detected";
					break;
				case Codes::BANDWIDTH_LIMIT_EXCEEDED:
					$result = "Bandwidth Limit Exceeded";
					break;
				case Codes::NOT_EXTENDED:
					$result = "Not Extended";
					break;
				case Codes::NETWORK_AUTHENTICATION_REQUIRED:
					$result = "Network Authentication Required";
					break;

				default:
					$result = "Unknown Response";
					break;
			}

			return $result;
		}
	}
?>
