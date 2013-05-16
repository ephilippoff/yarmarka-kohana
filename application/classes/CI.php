<?php defined('SYSPATH') OR die('No direct script access.');

class CI extends Kohana_URL {

	/**
	 * Gets the base URL to the application.
	 * To specify a protocol, provide the protocol as a string or request object.
	 * If a protocol is used, a complete URL will be generated using the
	 * `$_SERVER['HTTP_HOST']` variable.
	 *
	 *     // Absolute URL path with no host or protocol
	 *     echo URL::base();
	 *
	 *     // Absolute URL path with host, https protocol and index.php if set
	 *     echo URL::base('https', TRUE);
	 *
	 *     // Absolute URL path with host and protocol from $request
	 *     echo URL::base($request);
	 *
	 * @param   mixed    $protocol Protocol string, [Request], or boolean
	 * @param   boolean  $index    Add index file to URL?
	 * @return  string
	 * @uses    Kohana::$index_file
	 * @uses    Request::protocol()
	 */
	public static function base($protocol = NULL, $index = FALSE)
	{
		// Start with the configured base URL
		$base_url = Kohana::$base_url;

		if ($protocol === TRUE)
		{
			// Use the initial request to get the protocol
			$protocol = Request::$initial;
		}

		if ($protocol instanceof Request)
		{
			if ( ! $protocol->secure())
			{
				// Use the current protocol
				list($protocol) = explode('/', strtolower($protocol->protocol()));
			}
			else
			{
				$protocol = 'https';
			}
		}

		if ( ! $protocol)
		{
			// Use the configured default protocol
			$protocol = parse_url($base_url, PHP_URL_SCHEME);
		}

		if ($index === TRUE AND ! empty(Kohana::$index_file))
		{
			// Add the index file to the URL
			$base_url .= Kohana::$index_file.'/';
		}

		if (is_string($protocol))
		{
			if ($port = parse_url($base_url, PHP_URL_PORT))
			{
				// Found a port, make it usable for the URL
				$port = ':'.$port;
			}

			if ($domain = parse_url($base_url, PHP_URL_HOST))
			{
				// Remove everything but the path from the URL
				$base_url = parse_url($base_url, PHP_URL_PATH);
			}
			else
			{
				// Attempt to use HTTP_HOST and fallback to SERVER_NAME
				$domain = Kohana::$config->load('common.main_domain');
			}

			// Add the protocol and domain to the base URL
			$base_url = $protocol.'://'.$domain.$port.$base_url;
		}

		return $base_url;
	}

	/**
	 * Fetches an absolute site URL based on a URI segment.
	 *
	 *     echo URL::site('foo/bar');
	 *
	 * @param   string  $uri        Site URI to convert
	 * @param   mixed   $protocol   Protocol string or [Request] class to use protocol from
	 * @param   boolean $index		Include the index_page in the URL
	 * @return  string
	 * @uses    URL::base
	 */
	public static function site($uri = '', $protocol = 'http', $index = TRUE)
	{
		// Chop off possible scheme, host, port, user and pass parts
		$path = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));

		if ( ! UTF8::is_ascii($path))
		{
			// Encode all non-ASCII characters, as per RFC 1738
			$path = preg_replace_callback('~([^/]+)~', 'URL::_rawurlencode_callback', $path);
		}

		// Concat the URL
		return CI::base($protocol, $index).$path;
	}
}
