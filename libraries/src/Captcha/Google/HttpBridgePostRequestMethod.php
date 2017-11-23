<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Captcha\Google;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Http\HttpFactory;
use Joomla\Http\Exception\InvalidResponseCodeException;
use Joomla\Http\Http;
use ReCaptcha\RequestMethod;
use ReCaptcha\RequestParameters;

/**
 * Bridges the Joomla! HTTP API to the Google Recaptcha RequestMethod interface for a POST request.
 *
 * @since  4.0.0
 */
final class HttpBridgePostRequestMethod implements RequestMethod
{
	/**
	 * URL to which requests are sent.
	 *
	 * @const  string
	 * @since  4.0.0
	 */
	const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	/**
	 * The HTTP adapter
	 *
	 * @var    Http
	 * @since  4.0.0
	 */
	private $http;

	/**
	 * Class constructor.
	 *
	 * @param   Http|null  $http  The HTTP adapter
	 *
	 * @since   4.0.0
	 */
	public function __construct(Http $http = null)
	{
		$this->http = $http ?: HttpFactory::getHttp();
	}

	/**
	 * Submit the request with the specified parameters.
	 *
	 * @param   RequestParameters  $params  Request parameters
	 *
	 * @return  string  Body of the reCAPTCHA response
	 *
	 * @since   4.0.0
	 */
	public function submit(RequestParameters $params)
	{
		try
		{
			$response = $this->http->post(self::SITE_VERIFY_URL, $params->toArray());

			return (string) $response->getBody();
		}
		catch (InvalidResponseCodeException $exception)
		{
			return '';
		}
	}
}
