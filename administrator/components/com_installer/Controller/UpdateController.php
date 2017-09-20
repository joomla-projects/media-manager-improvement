<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Installer\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Utilities\ArrayHelper;

/**
 * Installer Update Controller
 *
 * @since  1.6
 */
class UpdateController extends BaseController
{
	/**
	 * Update a set of extensions.
	 *
	 * @since   1.6
	 */
	public function update()
	{
		// Check for request forgeries.
		\JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));

		/* @var \Joomla\Component\Installer\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('update');

		$uid = $this->input->get('cid', array(), 'array');
		$uid = ArrayHelper::toInteger($uid, array());

		// Get the minimum stability.
		$params        = ComponentHelper::getComponent('com_installer')->getParams();
		$minimum_stability = $params->get('minimum_stability', \JUpdater::STABILITY_STABLE, 'int');

		$model->update($uid, $minimum_stability);

		$app          = $this->app;
		$redirect_url = $app->getUserState('com_installer.redirect_url');

		// Don't redirect to an external URL.
		if (!\JUri::isInternal($redirect_url))
		{
			$redirect_url = '';
		}

		if (empty($redirect_url))
		{
			$redirect_url = \JRoute::_('index.php?option=com_installer&view=update', false);
		}
		else
		{
			// Wipe out the user state when we're going to redirect.
			$app->setUserState('com_installer.redirect_url', '');
			$app->setUserState('com_installer.message', '');
			$app->setUserState('com_installer.extension_message', '');
		}

		$this->setRedirect($redirect_url);
	}

	/**
	 * Find new updates.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function find()
	{
		(\JSession::checkToken() or \JSession::checkToken('get')) or jexit(\JText::_('JINVALID_TOKEN'));

		// Get the caching duration.
		$params        = ComponentHelper::getComponent('com_installer')->getParams();
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;

		// Get the minimum stability.
		$minimum_stability = $params->get('minimum_stability', \JUpdater::STABILITY_STABLE, 'int');

		// Find updates.
		/* @var \Joomla\Component\Installer\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('update');

		$disabledUpdateSites = $model->getDisabledUpdateSites();

		if ($disabledUpdateSites)
		{
			$updateSitesUrl = \JRoute::_('index.php?option=com_installer&view=updatesites');
			$this->setMessage(\JText::sprintf('COM_INSTALLER_MSG_UPDATE_SITES_COUNT_CHECK', $updateSitesUrl), 'warning');
		}

		$model->findUpdates(0, $cache_timeout, $minimum_stability);
		$this->setRedirect(\JRoute::_('index.php?option=com_installer&view=update', false));
	}

	/**
	 * Purges updates.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function purge()
	{
		// Check for request forgeries.
		\JSession::checkToken() or jexit(\JText::_('JINVALID_TOKEN'));

		/* @var \Joomla\Component\Installer\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('update');
		$model->purge();

		/**
		 * We no longer need to enable update sites in Joomla! 3.4 as we now allow the users to manage update sites
		 * themselves.
		 * $model->enableSites();
		 */

		$this->setRedirect(\JRoute::_('index.php?option=com_installer&view=update', false), $model->_message);
	}

	/**
	 * Fetch and report updates in \JSON format, for A\JAX requests
	 *
	 * @return void
	 *
	 * @since 2.5
	 */
	public function ajax()
	{
		$app = $this->app;

		if (!\JSession::checkToken('get'))
		{
			$app->setHeader('status', 403, true);
			$app->sendHeaders();
			echo \JText::_('JINVALID_TOKEN');
			$app->close();
		}

		$eid               = $this->input->getInt('eid', 0);
		$skip              = $this->input->get('skip', array(), 'array');
		$cache_timeout     = $this->input->getInt('cache_timeout', 0);
		$minimum_stability = $this->input->getInt('minimum_stability', -1);

		$params        = ComponentHelper::getComponent('com_installer')->getParams();

		if ($cache_timeout == 0)
		{
			$cache_timeout = $params->get('cachetimeout', 6, 'int');
			$cache_timeout = 3600 * $cache_timeout;
		}

		if ($minimum_stability < 0)
		{
			$minimum_stability = $params->get('minimum_stability', \JUpdater::STABILITY_STABLE, 'int');
		}

		/* @var \Joomla\Component\Installer\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('update');
		$model->findUpdates($eid, $cache_timeout, $minimum_stability);

		$model->setState('list.start', 0);
		$model->setState('list.limit', 0);

		if ($eid != 0)
		{
			$model->setState('filter.extension_id', $eid);
		}

		$updates = $model->getItems();

		if (!empty($skip))
		{
			$unfiltered_updates = $updates;
			$updates            = array();

			foreach ($unfiltered_updates as $update)
			{
				if (!in_array($update->extension_id, $skip))
				{
					$updates[] = $update;
				}
			}
		}

		echo json_encode($updates);

		$app->close();
	}
}
