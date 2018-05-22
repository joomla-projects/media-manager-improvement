<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.redirect
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\ErrorEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use Joomla\Event\SubscriberInterface;
use Joomla\String\StringHelper;

/**
 * Plugin class for redirect handling.
 *
 * @since  1.6
 */
class PlgSystemRedirect extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  3.4
	 */
	protected $autoloadLanguage = false;

	/**
	 * Database object.
	 *
	 * @var    DatabaseInterface
	 * @since  __DEPLOY_VERSION__
	 */
	protected $db;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onError' => 'handleError',
		];
	}

	/**
	 * Internal processor for all error handlers
	 *
	 * @param   ErrorEvent  $event  The event object
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public function handleError(ErrorEvent $event)
	{
		/** @var \Joomla\CMS\Application\CMSApplication $app */
		$app = $event->getApplication();

		if ($app->isClient('administrator') || ((int) $event->getError()->getCode() !== 404))
		{
			return;
		}

		$uri = Uri::getInstance();

		// These are the original URLs
		$orgurl                = rawurldecode($uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment')));
		$orgurlRel             = rawurldecode($uri->toString(array('path', 'query', 'fragment')));

		// The above doesn't work for sub directories, so do this
		$orgurlRootRel         = str_replace(Uri::root(), '', $orgurl);

		// For when users have added / to the url
		$orgurlRootRelSlash    = str_replace(Uri::root(), '/', $orgurl);
		$orgurlWithoutQuery    = rawurldecode($uri->toString(array('scheme', 'host', 'port', 'path', 'fragment')));
		$orgurlRelWithoutQuery = rawurldecode($uri->toString(array('path', 'fragment')));

		// These are the URLs we save and use
		$url                = StringHelper::strtolower(rawurldecode($uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment'))));
		$urlRel             = StringHelper::strtolower(rawurldecode($uri->toString(array('path', 'query', 'fragment'))));

		// The above doesn't work for sub directories, so do this
		$urlRootRel         = str_replace(Uri::root(), '', $url);

		// For when users have added / to the url
		$urlRootRelSlash    = str_replace(Uri::root(), '/', $url);
		$urlWithoutQuery    = StringHelper::strtolower(rawurldecode($uri->toString(array('scheme', 'host', 'port', 'path', 'fragment'))));
		$urlRelWithoutQuery = StringHelper::strtolower(rawurldecode($uri->toString(array('path', 'fragment'))));

		$excludes = (array) $this->params->get('exclude_urls');

		$skipUrl = false;

		foreach ($excludes as $exclude)
		{
			if (empty($exclude->term))
			{
				continue;
			}

			if (!empty($exclude->regexp))
			{
				// Only check $url, because it includes all other sub urls
				if (preg_match('/' . $exclude->term . '/i', $orgurlRel))
				{
					$skipUrl = true;
					break;
				}
			}
			else
			{
				if (StringHelper::strpos($orgurlRel, $exclude->term))
				{
					$skipUrl = true;
					break;
				}
			}
		}

		// Why is this (still) here?
		if ($skipUrl || (strpos($url, 'mosConfig_') !== false) || (strpos($url, '=http://') !== false))
		{
			return;
		}

		$query = $this->db->getQuery(true);

		$query->select('*')
			->from($this->db->quoteName('#__redirect_links'))
			->where(
				'('
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($url)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($urlRel)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($urlRootRel)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($urlRootRelSlash)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($urlWithoutQuery)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($urlRelWithoutQuery)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($orgurl)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($orgurlRel)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($orgurlRootRel)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($orgurlRootRelSlash)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($orgurlWithoutQuery)
				. ' OR '
				. $this->db->quoteName('old_url') . ' = ' . $this->db->quote($orgurlRelWithoutQuery)
				. ')'
			);

		$this->db->setQuery($query);

		$redirect = null;

		try
		{
			$redirects = $this->db->loadAssocList();
		}
		catch (Exception $e)
		{
			$event->setError(new Exception(Text::_('PLG_SYSTEM_REDIRECT_ERROR_UPDATING_DATABASE'), 500, $e));

			return;
		}

		$possibleMatches = array_unique(
			array(
				$url,
				$urlRel,
				$urlRootRel,
				$urlRootRelSlash,
				$urlWithoutQuery,
				$urlRelWithoutQuery,
				$orgurl,
				$orgurlRel,
				$orgurlRootRel,
				$orgurlRootRelSlash,
				$orgurlWithoutQuery,
				$orgurlRelWithoutQuery,
			)
		);

		foreach ($possibleMatches as $match)
		{
			if (($index = array_search($match, array_column($redirects, 'old_url'))) !== false)
			{
				$redirect = (object) $redirects[$index];

				if ((int) $redirect->published === 1)
				{
					break;
				}
			}
		}

		// A redirect object was found and, if published, will be used
		if ($redirect !== null && ((int) $redirect->published === 1))
		{
			if (!$redirect->header || (bool) ComponentHelper::getParams('com_redirect')->get('mode', false) === false)
			{
				$redirect->header = 301;
			}

			if ($redirect->header < 400 && $redirect->header >= 300)
			{
				$urlQuery = $uri->getQuery();

				$oldUrlParts = parse_url($redirect->old_url);

				if ($urlQuery !== '' && empty($oldUrlParts['query']))
				{
					$redirect->new_url .= '?' . $urlQuery;
				}

				$dest = Uri::isInternal($redirect->new_url) || strpos($redirect->new_url, 'http') === false ?
					Route::_($redirect->new_url) : $redirect->new_url;

				// In case the url contains double // lets remove it
				$destination = str_replace(Uri::root() . '/', Uri::root(), $dest);

				$app->redirect($destination, (int) $redirect->header);
			}

			$event->setError(new RuntimeException($event->getError()->getMessage(), $redirect->header, $event->getError()));

			return;
		}
		// No redirect object was found so we create an entry in the redirect table
		elseif ($redirect === null)
		{
			if ((bool) $this->params->get('collect_urls', true))
			{
				$data = (object) array(
					'id' => 0,
					'old_url' => $url,
					'referer' => $app->input->server->getString('HTTP_REFERER', ''),
					'hits' => 1,
					'published' => 0,
					'created_date' => JFactory::getDate()->toSql()
				);

				try
				{
					$this->db->insertObject('#__redirect_links', $data, 'id');
				}
				catch (Exception $e)
				{
					$event->setError(new Exception(Text::_('PLG_SYSTEM_REDIRECT_ERROR_UPDATING_DATABASE'), 500, $e));

					return;
				}
			}
		}
		// We have an unpublished redirect object, increment the hit counter
		else
		{
			$redirect->hits++;

			try
			{
				$this->db->updateObject('#__redirect_links', $redirect, 'id');
			}
			catch (Exception $e)
			{
				$event->setError(new Exception(Text::_('PLG_SYSTEM_REDIRECT_ERROR_UPDATING_DATABASE'), 500, $e));

				return;
			}
		}
	}
}
