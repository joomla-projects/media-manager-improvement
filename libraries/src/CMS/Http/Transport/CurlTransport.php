<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Http\Transport;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Http\Response;
use Joomla\CMS\Http\TransportInterface;
use Joomla\CMS\Uri\Uri;
use Joomla\Http\AbstractTransport;
use Joomla\Http\Exception\InvalidResponseCodeException;
use Joomla\Uri\UriInterface;
use Zend\Diactoros\Stream as StreamResponse;

/**
 * HTTP transport class for using cURL.
 *
 * @since  11.3
 */
class CurlTransport extends AbstractTransport implements TransportInterface
{
	/**
	 * Send a request to the server and return a Response object with the response.
	 *
	 * @param   string        $method     The HTTP method for sending the request.
	 * @param   UriInterface  $uri        The URI to the resource to request.
	 * @param   mixed         $data       Either an associative array or a string to be sent with the request.
	 * @param   array         $headers    An array of request headers to send with the request.
	 * @param   integer       $timeout    Read timeout in seconds.
	 * @param   string        $userAgent  The optional user agent string to send with the request.
	 *
	 * @return  Response
	 *
	 * @since   11.3
	 * @throws  \RuntimeException
	 */
	public function request($method, UriInterface $uri, $data = null, array $headers = [], $timeout = null, $userAgent = null)
	{
		// Setup the cURL handle.
		$ch = curl_init();

		$options = array();

		// Set the request method.
		switch (strtoupper($method))
		{
			case 'GET':
				$options[CURLOPT_HTTPGET] = true;
				break;

			case 'POST':
				$options[CURLOPT_POST] = true;
				break;

			case 'PUT':
			default:
				$options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
				break;
		}

		// Don't wait for body when $method is HEAD
		$options[CURLOPT_NOBODY] = ($method === 'HEAD');

		// Initialize the certificate store
		$options[CURLOPT_CAINFO] = $this->getOption('curl.certpath', __DIR__ . '/cacert.pem');

		// If data exists let's encode it and make sure our Content-type header is set.
		if (isset($data))
		{
			// If the data is a scalar value simply add it to the cURL post fields.
			if (is_scalar($data) || (isset($headers['Content-Type']) && strpos($headers['Content-Type'], 'multipart/form-data') === 0))
			{
				$options[CURLOPT_POSTFIELDS] = $data;
			}

			// Otherwise we need to encode the value first.
			else
			{
				$options[CURLOPT_POSTFIELDS] = http_build_query($data);
			}

			if (!isset($headers['Content-Type']))
			{
				$headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
			}

			// Add the relevant headers.
			if (is_scalar($options[CURLOPT_POSTFIELDS]))
			{
				$headers['Content-Length'] = strlen($options[CURLOPT_POSTFIELDS]);
			}
		}

		// Build the headers string for the request.
		$headerArray = array();

		if (isset($headers))
		{
			foreach ($headers as $key => $value)
			{
				$headerArray[] = $key . ': ' . $value;
			}

			// Add the headers string into the stream context options array.
			$options[CURLOPT_HTTPHEADER] = $headerArray;
		}

		// Curl needs the accepted encoding header as option
		if (isset($headers['Accept-Encoding']))
		{
			$options[CURLOPT_ENCODING] = $headers['Accept-Encoding'];
		}

		// If an explicit timeout is given user it.
		if (isset($timeout))
		{
			$options[CURLOPT_TIMEOUT] = (int) $timeout;
			$options[CURLOPT_CONNECTTIMEOUT] = (int) $timeout;
		}

		// If an explicit user agent is given use it.
		if (isset($userAgent))
		{
			$options[CURLOPT_USERAGENT] = $userAgent;
		}

		// Set the request URL.
		$options[CURLOPT_URL] = (string) $uri;

		// We want our headers. :-)
		$options[CURLOPT_HEADER] = true;

		// Return it... echoing it would be tacky.
		$options[CURLOPT_RETURNTRANSFER] = true;

		// Override the Expect header to prevent cURL from confusing itself in its own stupidity.
		// Link: http://the-stickman.com/web-development/php-and-curl-disabling-100-continue-header/
		$options[CURLOPT_HTTPHEADER][] = 'Expect:';

		// Follow redirects if server config allows
		if ($this->redirectsAllowed())
		{
			$options[CURLOPT_FOLLOWLOCATION] = (bool) $this->getOption('follow_location', true);
		}

		// Proxy configuration
		$config = \JFactory::getConfig();

		if ($config->get('proxy_enable'))
		{
			$options[CURLOPT_PROXY] = $config->get('proxy_host') . ':' . $config->get('proxy_port');

			if ($user = $config->get('proxy_user'))
			{
				$options[CURLOPT_PROXYUSERPWD] = $user . ':' . $config->get('proxy_pass');
			}
		}

		// Set any custom transport options
		foreach ($this->getOption('transport.curl', array()) as $key => $value)
		{
			$options[$key] = $value;
		}

		// Authentification, if needed
		if ($this->getOption('userauth') && $this->getOption('passwordauth'))
		{
			$options[CURLOPT_USERPWD] = $this->getOption('userauth') . ':' . $this->getOption('passwordauth');
			$options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
		}

		// Set the cURL options.
		curl_setopt_array($ch, $options);

		// Execute the request and close the connection.
		$content = curl_exec($ch);

		// Check if the content is a string. If it is not, it must be an error.
		if (!is_string($content))
		{
			$message = curl_error($ch);

			if (empty($message))
			{
				// Error but nothing from cURL? Create our own
				$message = 'No HTTP response received';
			}

			throw new \RuntimeException($message);
		}

		// Get the request information.
		$info = curl_getinfo($ch);

		// Close the connection.
		curl_close($ch);

		$response = $this->getResponse($content, $info);

		// Manually follow redirects if server doesn't allow to follow location using curl
		if ($response->code >= 301 && $response->code < 400 && isset($response->headers['Location']) && (bool) $this->getOption('follow_location', true))
		{
			$redirect_uri = new Uri($response->headers['Location']);
			if (in_array($redirect_uri->getScheme(), array('file', 'scp')))
			{
				throw new \RuntimeException('Curl redirect cannot be used in file or scp requests.');
			}
			$response = $this->request($method, $redirect_uri, $data, $headers, $timeout, $userAgent);
		}

		return $response;
	}

	/**
	 * Method to get a response object from a server response.
	 *
	 * @param   string  $content  The complete server response, including headers
	 *                            as a string if the response has no errors.
	 * @param   array   $info     The cURL request information.
	 *
	 * @return  Response
	 *
	 * @since   11.3
	 * @throws  InvalidResponseCodeException
	 */
	protected function getResponse($content, $info)
	{
		// Try to get header size
		if (isset($info['header_size']))
		{
			$headerString = trim(substr($content, 0, $info['header_size']));
			$headerArray  = explode("\r\n\r\n", $headerString);

			// Get the last set of response headers as an array.
			$headers = explode("\r\n", array_pop($headerArray));

			// Set the body for the response.
			$body = substr($content, $info['header_size']);
		}
		// Fallback and try to guess header count by redirect count
		else
		{
			// Get the number of redirects that occurred.
			$redirects = isset($info['redirect_count']) ? $info['redirect_count'] : 0;

			/*
			 * Split the response into headers and body. If cURL encountered redirects, the headers for the redirected requests will
			 * also be included. So we split the response into header + body + the number of redirects and only use the last two
			 * sections which should be the last set of headers and the actual body.
			 */
			$response = explode("\r\n\r\n", $content, 2 + $redirects);

			// Set the body for the response.
			$body = array_pop($response);

			// Get the last set of response headers as an array.
			$headers = explode("\r\n", array_pop($response));
		}

		// Get the response code from the first offset of the response headers.
		preg_match('/[0-9]{3}/', array_shift($headers), $matches);

		$code = count($matches) ? $matches[0] : null;

		if (!is_numeric($code))
		{
			// No valid response code was detected.
			throw new InvalidResponseCodeException('No HTTP response code found.');
		}

		$statusCode      = (int) $code;
		$verifiedHeaders = $this->processHeaders($headers);

		$streamInterface = new StreamResponse('php://memory', 'rw');
		$streamInterface->write($body);

		return new Response($streamInterface, $statusCode, $verifiedHeaders);
	}

	/**
	 * Method to check if HTTP transport cURL is available for use
	 *
	 * @return boolean true if available, else false
	 *
	 * @since   12.1
	 */
	public static function isSupported()
	{
		return function_exists('curl_version') && curl_version();
	}

	/**
	 * Check if redirects are allowed
	 *
	 * @return  boolean
	 *
	 * @since   12.1
	 */
	private function redirectsAllowed()
	{
		$curlVersion = curl_version();

		// If open_basedir is enabled we also need to check if libcurl version is 7.19.4 or higher
		if (!ini_get('open_basedir') || version_compare($curlVersion['version'], '7.19.4', '>='))
		{
			return true;
		}

		return false;
	}
}
