<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Application;

defined('JPATH_PLATFORM') or die;

use Joomla\Application\AbstractWebApplication;
use Joomla\Application\Web\WebClient;
use Joomla\Event\DispatcherAwareInterface;
use Joomla\Event\DispatcherAwareTrait;
use Joomla\Registry\Registry;
use Joomla\Session\SessionEvent;
use Joomla\String\StringHelper;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for a Joomla! Web application.
 *
 * @since  11.4
 */
abstract class WebApplication extends AbstractWebApplication implements DispatcherAwareInterface
{
	use Autoconfigurable, DispatcherAwareTrait, EventAware, IdentityAware;

	/**
	 * The application document object.
	 *
	 * @var    \JDocument
	 * @since  11.3
	 */
	protected $document;

	/**
	 * The application language object.
	 *
	 * @var    \JLanguage
	 * @since  11.3
	 */
	protected $language;

	/**
	 * The application instance.
	 *
	 * @var    static
	 * @since  11.3
	 */
	protected static $instance;

	/**
	 * A map of integer HTTP 1.1 response codes to the full HTTP Status for the headers.
	 *
	 * @var    array
	 * @since  3.4
	 * @see    http://tools.ietf.org/pdf/rfc7231.pdf
	 */
	private $responseMap = array(
		300 => 'HTTP/1.1 300 Multiple Choices',
		301 => 'HTTP/1.1 301 Moved Permanently',
		302 => 'HTTP/1.1 302 Found',
		303 => 'HTTP/1.1 303 See other',
		304 => 'HTTP/1.1 304 Not Modified',
		305 => 'HTTP/1.1 305 Use Proxy',
		306 => 'HTTP/1.1 306 (Unused)',
		307 => 'HTTP/1.1 307 Temporary Redirect',
		308 => 'HTTP/1.1 308 Permanent Redirect',
	);

	/**
     * A map of HTTP Response headers which may only send a single value, all others
     * are considered to allow multiple
     *
     * @var    object
     * @since  3.5.2
     * @see    https://tools.ietf.org/html/rfc7230
     */
	private $singleValueResponseHeaders = array(
		'status', // This is not a valid header name, but the representation used by Joomla to identify the HTTP Response Code
		'Content-Length',
		'Host',
		'Content-Type',
		'Content-Location',
		'Date',
		'Location',
		'Retry-After',
		'Server',
		'Mime-Version',
		'Last-Modified',
		'ETag',
		'Accept-Ranges',
		'Content-Range',
		'Age',
		'Expires'
	);

	/**
	 * Class constructor.
	 *
	 * @param   \JInput            $input     An optional argument to provide dependency injection for the application's
	 *                                        input object.  If the argument is a JInput object that object will become
	 *                                        the application's input object, otherwise a default input object is created.
	 * @param   Registry           $config    An optional argument to provide dependency injection for the application's
	 *                                        config object.  If the argument is a Registry object that object will become
	 *                                        the application's config object, otherwise a default config object is created.
	 * @param   WebClient          $client    An optional argument to provide dependency injection for the application's
	 *                                        client object.  If the argument is a WebClient object that object will become
	 *                                        the application's client object, otherwise a default client object is created.
	 * @param   ResponseInterface  $response  An optional argument to provide dependency injection for the application's
	 *                                        response object.  If the argument is a ResponseInterface object that object
	 *                                        will become the application's response object, otherwise a default response
	 *                                        object is created.
	 *
	 * @since   11.3
	 */
	public function __construct(\JInput $input = null, Registry $config = null, WebClient $client = null, ResponseInterface $response = null)
	{
		// Ensure we have a \JInput object otherwise the DI for \Joomla\CMS\Session\Storage\JoomlaStorage fails
		$input = $input ?: new \JInput;

		parent::__construct($input, $config, $client, $response);

		// Load the configuration object.
		$this->loadConfiguration($this->fetchConfigurationData());

		// Set the execution datetime and timestamp;
		$this->set('execution.datetime', gmdate('Y-m-d H:i:s'));
		$this->set('execution.timestamp', time());

		// Set the system URIs.
		$this->loadSystemUris();
	}

	/**
	 * Returns a reference to the global WebApplication object, only creating it if it doesn't already exist.
	 *
	 * This method must be invoked as: $web = WebApplication::getInstance();
	 *
	 * @param   string  $name  The name (optional) of the JApplicationWeb class to instantiate.
	 *
	 * @return  WebApplication
	 *
	 * @since   11.3
	 * @throws  \RuntimeException
	 */
	public static function getInstance($name = null)
	{
		// Only create the object if it doesn't exist.
		if (empty(static::$instance))
		{
			if (!is_subclass_of($name, '\\Joomla\\CMS\\Application\\WebApplication'))
			{
				throw new \RuntimeException(sprintf('Unable to load application: %s', $name), 500);
			}

			static::$instance = new $name;
		}

		return static::$instance;
	}

	/**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function execute()
	{
		// Trigger the onBeforeExecute event.
		$this->triggerEvent('onBeforeExecute');

		// Perform application routines.
		$this->doExecute();

		// Trigger the onAfterExecute event.
		$this->triggerEvent('onAfterExecute');

		// If we have an application document object, render it.
		if ($this->document instanceof \JDocument)
		{
			// Trigger the onBeforeRender event.
			$this->triggerEvent('onBeforeRender');

			// Render the application output.
			$this->render();

			// Trigger the onAfterRender event.
			$this->triggerEvent('onAfterRender');
		}

		// If gzip compression is enabled in configuration and the server is compliant, compress the output.
		if ($this->get('gzip') && !ini_get('zlib.output_compression') && (ini_get('output_handler') != 'ob_gzhandler'))
		{
			$this->compress();
		}

		// Trigger the onBeforeRespond event.
		$this->triggerEvent('onBeforeRespond');

		// Send the application response.
		$this->respond();

		// Trigger the onAfterRespond event.
		$this->triggerEvent('onAfterRespond');
	}

	/**
	 * Rendering is the process of pushing the document buffers into the template
	 * placeholders, retrieving data from the document and pushing it into
	 * the application response buffer.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function render()
	{
		// Setup the document options.
		$options = array(
			'template' => $this->get('theme'),
			'file' => $this->get('themeFile', 'index.php'),
			'params' => $this->get('themeParams'),
		);

		if ($this->get('themes.base'))
		{
			$options['directory'] = $this->get('themes.base');
		}
		// Fall back to constants.
		else
		{
			$options['directory'] = defined('JPATH_THEMES') ? JPATH_THEMES : (defined('JPATH_BASE') ? JPATH_BASE : __DIR__) . '/themes';
		}

		// Parse the document.
		$this->document->parse($options);

		// Render the document.
		$data = $this->document->render($this->get('cache_enabled'), $options);

		// Set the application output data.
		$this->setBody($data);
	}

	/**
	 * Redirect to another URL.
	 *
	 * If the headers have not been sent the redirect will be accomplished using a "301 Moved Permanently"
	 * or "303 See Other" code in the header pointing to the new location. If the headers have already been
	 * sent this will be accomplished using a JavaScript statement.
	 *
	 * @param   string   $url     The URL to redirect to. Can only be http/https URL.
	 * @param   integer  $status  The HTTP 1.1 status code to be provided. 303 is assumed by default.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public function redirect($url, $status = 303)
	{
		// Check for relative internal links.
		if (preg_match('#^index\.php#', $url))
		{
			// We changed this from "$this->get('uri.base.full') . $url" due to the inability to run the system tests with the original code
			$url = \JUri::base() . $url;
		}

		// Perform a basic sanity check to make sure we don't have any CRLF garbage.
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		/*
		 * Here we need to check and see if the URL is relative or absolute.  Essentially, do we need to
		 * prepend the URL with our base URL for a proper redirect.  The rudimentary way we are looking
		 * at this is to simply check whether or not the URL string has a valid scheme or not.
		 */
		if (!preg_match('#^[a-z]+\://#i', $url))
		{
			// Get a \JUri instance for the requested URI.
			$uri = \JUri::getInstance($this->get('uri.request'));

			// Get a base URL to prepend from the requested URI.
			$prefix = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

			// We just need the prefix since we have a path relative to the root.
			if ($url[0] == '/')
			{
				$url = $prefix . $url;
			}
			// It's relative to where we are now, so lets add that.
			else
			{
				$parts = explode('/', $uri->toString(array('path')));
				array_pop($parts);
				$path = implode('/', $parts) . '/';
				$url = $prefix . $path . $url;
			}
		}

		// If the headers have already been sent we need to send the redirect statement via JavaScript.
		if ($this->checkHeadersSent())
		{
			echo "<script>document.location.href='" . str_replace("'", '&apos;', $url) . "';</script>\n";
		}
		else
		{
			// We have to use a JavaScript redirect here because MSIE doesn't play nice with utf-8 URLs.
			if (($this->client->engine == WebClient::TRIDENT) && !StringHelper::is_ascii($url))
			{
				$html = '<html><head>';
				$html .= '<meta http-equiv="content-type" content="text/html; charset=' . $this->charSet . '">';
				$html .= '<script>document.location.href=\'' . str_replace("'", '&apos;', $url) . '\';</script>';
				$html .= '</head><body></body></html>';

				echo $html;
			}
			else
			{
				// Check if we have a boolean for the status variable for compatability with old $move parameter
				// @deprecated 4.0
				if (is_bool($status))
				{
					$status = $status ? 301 : 303;
				}

				// Now check if we have an integer status code that maps to a valid redirect. If we don't then set a 303
				// @deprecated 4.0 From 4.0 if no valid status code is given an \InvalidArgumentException will be thrown
				if (!is_int($status) || is_int($status) && !isset($this->responseMap[$status]))
				{
					$status = 303;
				}

				// All other cases use the more efficient HTTP header for redirection.
				$this->header($this->responseMap[$status]);
				$this->header('Location: ' . $url);
			}
		}

		// Set appropriate headers
		$this->respond();

		//  Close the application after the redirect.
		$this->close();
	}

	/**
	 * Method to get the application document object.
	 *
	 * @return  \JDocument  The document object
	 *
	 * @since   11.3
	 */
	public function getDocument()
	{
		return $this->document;
	}

	/**
	 * Method to get the application language object.
	 *
	 * @return  \JLanguage  The language object
	 *
	 * @since   11.3
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Flush the media version to refresh versionable assets
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function flushAssets()
	{
		$version = new \JVersion;
		$version->refreshMediaVersion();
	}

	/**
	 * Allows the application to load a custom or default document.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create a document,
	 * if required, based on more specific needs.
	 *
	 * @param   \JDocument  $document  An optional document object. If omitted, the factory document is created.
	 *
	 * @return  WebApplication This method is chainable.
	 *
	 * @since   11.3
	 */
	public function loadDocument(\JDocument $document = null)
	{
		$this->document = ($document === null) ? \JFactory::getDocument() : $document;

		return $this;
	}

	/**
	 * Allows the application to load a custom or default language.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create a language,
	 * if required, based on more specific needs.
	 *
	 * @param   \JLanguage  $language  An optional language object. If omitted, the factory language is created.
	 *
	 * @return  WebApplication This method is chainable.
	 *
	 * @since   11.3
	 */
	public function loadLanguage(\JLanguage $language = null)
	{
		$this->language = ($language === null) ? \JFactory::getLanguage() : $language;

		return $this;
	}

	/**
	 * Allows the application to load a custom or default session.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create a session,
	 * if required, based on more specific needs.
	 *
	 * @param   \JSession  $session  An optional session object. If omitted, the session is created.
	 *
	 * @return  WebApplication This method is chainable.
	 *
	 * @since   11.3
	 * @deprecated  5.0  The session should be injected as a service.
	 */
	public function loadSession(\JSession $session = null)
	{
		$this->getLogger()->warning(__METHOD__ . '() is deprecated.  Inject the session as a service instead.', array('category' => 'deprecated'));

		return $this;
	}

	/**
	 * After the session has been started we need to populate it with some default values.
	 *
	 * @param   SessionEvent  $event  Session event being triggered
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function afterSessionStart(SessionEvent $event)
	{
		$session = $event->getSession();

		if ($session->isNew())
		{
			$session->set('registry', new Registry);
			$session->set('user', new \JUser);
		}
	}

	/**
	 * Method to load the system URI strings for the application.
	 *
	 * @param   string  $requestUri  An optional request URI to use instead of detecting one from the
	 *                               server environment variables.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	protected function loadSystemUris($requestUri = null)
	{
		// Set the request URI.
		// @codeCoverageIgnoreStart
		if (!empty($requestUri))
		{
			$this->set('uri.request', $requestUri);
		}
		else
		{
			$this->set('uri.request', $this->detectRequestUri());
		}
		// @codeCoverageIgnoreEnd

		// Check to see if an explicit base URI has been set.
		$siteUri = trim($this->get('site_uri'));

		if ($siteUri != '')
		{
			$uri = \JUri::getInstance($siteUri);
			$path = $uri->toString(array('path'));
		}
		// No explicit base URI was set so we need to detect it.
		else
		{
			// Start with the requested URI.
			$uri = \JUri::getInstance($this->get('uri.request'));

			// If we are working from a CGI SAPI with the 'cgi.fix_pathinfo' directive disabled we use PHP_SELF.
			if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI']))
			{
				// We aren't expecting PATH_INFO within PHP_SELF so this should work.
				$path = dirname($_SERVER['PHP_SELF']);
			}
			// Pretty much everything else should be handled with SCRIPT_NAME.
			else
			{
				$path = dirname($_SERVER['SCRIPT_NAME']);
			}
		}

		$host = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

		// Check if the path includes "index.php".
		if (strpos($path, 'index.php') !== false)
		{
			// Remove the index.php portion of the path.
			$path = substr_replace($path, '', strpos($path, 'index.php'), 9);
		}

		$path = rtrim($path, '/\\');

		// Set the base URI both as just a path and as the full URI.
		$this->set('uri.base.full', $host . $path . '/');
		$this->set('uri.base.host', $host);
		$this->set('uri.base.path', $path . '/');

		// Set the extended (non-base) part of the request URI as the route.
		if (stripos($this->get('uri.request'), $this->get('uri.base.full')) === 0)
		{
			$this->set('uri.route', substr_replace($this->get('uri.request'), '', 0, strlen($this->get('uri.base.full'))));
		}

		// Get an explicitly set media URI is present.
		$mediaURI = trim($this->get('media_uri'));

		if ($mediaURI)
		{
			if (strpos($mediaURI, '://') !== false)
			{
				$this->set('uri.media.full', $mediaURI);
				$this->set('uri.media.path', $mediaURI);
			}
			else
			{
				// Normalise slashes.
				$mediaURI = trim($mediaURI, '/\\');
				$mediaURI = !empty($mediaURI) ? '/' . $mediaURI . '/' : '/';
				$this->set('uri.media.full', $this->get('uri.base.host') . $mediaURI);
				$this->set('uri.media.path', $mediaURI);
			}
		}
		// No explicit media URI was set, build it dynamically from the base uri.
		else
		{
			$this->set('uri.media.full', $this->get('uri.base.full') . 'media/');
			$this->set('uri.media.path', $this->get('uri.base.path') . 'media/');
		}
	}
}
