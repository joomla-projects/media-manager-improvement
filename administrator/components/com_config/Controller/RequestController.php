<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Config\Administrator\Controller;

defined('_JEXEC') or die;

use  \Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\Config\Site\Model\ConfigModel;

/**
 * Requests from the frontend
 *
 * @since  4.0.0
 */
class RequestController extends BaseController
{
	/**
	 * Execute the controller.
	 *
	 * @return  mixed  A rendered view or true
	 *
	 * @since   3.2
	 */
	public function getJson()
	{
		// Get the document object.
		$document = \JFactory::getDocument();

		$componentFolder = $this->input->getWord('option', 'com_config');

		if ($this->app->isClient('administrator'))
		{
			$viewName = $this->input->getWord('view', 'application');
		}
		else
		{
			$viewName = $this->input->getWord('view', 'config');
		}

		// Register the layout paths for the view
		$paths = new \SplPriorityQueue;

		if ($this->app->isClient('administrator'))
		{
			$paths->insert(JPATH_ADMINISTRATOR . '/components/' . $componentFolder . '/view/' . $viewName . '/tmpl', 1);
		}
		else
		{
			$paths->insert(JPATH_BASE . '/components/' . $componentFolder . '/view/' . $viewName . '/tmpl', 1);
		}

		$model     = new \Joomla\Component\Config\Administrator\Model\ApplicationModel;
		$component = $model->getState()->get('component.option');

		// Access check.
		if (!$this->app->getIdentity()->authorise('core.admin', $component)
			&& !$this->app->getIdentity()->authorise('core.options', $component))
		{
			$this->app->enqueueMessage(\JText::_('JERROR_ALERTNOAUTHOR'), 'error');

			return;
		}

		try
		{
			$data = $model->getData();
			$user = $this->app->getIdentity();
		}
		catch (\Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		$this->userIsSuperAdmin = $user->authorise('core.admin');

		// Required data
		$requiredData = array(
			'sitename'            => null,
			'offline'             => null,
			'access'              => null,
			'list_limit'          => null,
			'MetaDesc'            => null,
			'MetaKeys'            => null,
			'MetaRights'          => null,
			'sef'                 => null,
			'sitename_pagetitles' => null,
			'debug'               => null,
			'debug_lang'          => null,
			'error_reporting'     => null,
			'mailfrom'            => null,
			'fromname'            => null
		);

		$data = array_intersect_key($data, $requiredData);

		return json_encode($data);
	}
}
