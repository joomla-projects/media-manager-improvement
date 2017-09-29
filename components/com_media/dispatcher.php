<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\Dispatcher;

/**
 * Dispatcher class for com_media
 *
 * @since  __DEPLOY_VERSION__
 */
class MediaDispatcher extends Dispatcher
{
	/**
	 * The extension namespace
	 *
	 * @var    string
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $namespace = 'Joomla\\Component\\Media';

	public function __construct(\Joomla\CMS\Application\CMSApplication $app, \JInput $input = null)
	{
		if ($app->isClient('site'))
		{
			$app = \Joomla\CMS\Application\CMSApplication::getInstance('administrator');
		}

		parent::__construct($app, $input);

		$this->input->set('view', 'media');
	}

	/**
	 * Method to check component access permission
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @return  void
	 */
	protected function checkAccess()
	{
		$user   = $this->app->getIdentity();
		$asset  = $this->input->get('asset');
		$author = $this->input->get('author');

		// Access check
		if (!$user->authorise('core.manage', 'com_media')
			&& (!$asset || (!$user->authorise('core.edit', $asset)
					&& !$user->authorise('core.create', $asset)
					&& count($user->getAuthorisedCategories($asset, 'core.create')) == 0)
				&& !($user->id == $author && $user->authorise('core.edit.own', $asset))))
		{
			throw new \Joomla\CMS\Access\Exception\Notallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}

	/**
	 * Get a controller from the component
	 *
	 * @param   string  $name    Controller name
	 * @param   string  $client  Optional client (like Administrator, Site etc.)
	 * @param   array   $config  Optional controller config
	 *
	 * @return  Controller
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getController($name, $client = null, $config = array())
	{
		$config['base_path'] = JPATH_ADMINISTRATOR . '/components/com_media';

		return parent::getController($name, $client, $config);
	}
}
