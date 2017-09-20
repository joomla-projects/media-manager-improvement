<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Config\Administrator\View\Application;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Config\Administrator\Helper\ConfigHelper;

/**
 * View for the global configuration
 *
 * @since  3.2
 */
class HtmlView extends BaseHtmlView
{
	public $state;

	public $form;

	public $data;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     \JViewLegacy::loadTemplate()
	 * @since   3.0
	 */
	public function display($tpl = null)
	{
		$form = null;
		$data = null;

		try
		{
			// Load Form and Data
			$form = $this->get('form');
			$data = $this->get('data');
			$user = \JFactory::getUser();
		}
		catch (\Exception $e)
		{
			\JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		// Bind data
		if ($form && $data)
		{
			$form->bind($data);
		}

		// Get the params for com_users.
		$usersParams = ComponentHelper::getParams('com_users');

		// Get the params for com_media.
		$mediaParams = ComponentHelper::getParams('com_media');

		// Load settings for the FTP layer.
		$ftp = \JClientHelper::setCredentialsFromRequest('ftp');

		$this->form        = &$form;
		$this->data        = &$data;
		$this->ftp         = &$ftp;
		$this->usersParams = &$usersParams;
		$this->mediaParams = &$mediaParams;
		$this->components  = ConfigHelper::getComponentsWithConfig();
		ConfigHelper::loadLanguageForComponents($this->components);

		$this->userIsSuperAdmin = $user->authorise('core.admin');

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since	3.2
	 */
	protected function addToolbar()
	{
		ToolbarHelper::title(\JText::_('COM_CONFIG_GLOBAL_CONFIGURATION'), 'equalizer config');
		ToolbarHelper::saveGroup(
			[
				['apply', 'application.apply'],
				['save', 'application.save']
			],
			'btn-success'
		);
		ToolbarHelper::divider();
		ToolbarHelper::cancel('application.cancel');
		ToolbarHelper::divider();
		ToolbarHelper::help('JHELP_SITE_GLOBAL_CONFIGURATION');
	}
}
