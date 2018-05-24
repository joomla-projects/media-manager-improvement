<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Installer\Administrator\View\Warnings;

defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Installer\Administrator\View\Installer\HtmlView as InstallerViewDefault;

/**
 * Extension Manager Warning View
 *
 * @since  1.6
 */
class HtmlView extends InstallerViewDefault
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		$items = $this->get('Items');
		$this->messages = &$items;
		parent::display($tpl);

		if (count($items) > 0)
		{
			\JFactory::getApplication()->enqueueMessage(\JText::_('COM_INSTALLER_MSG_WARNINGS_NOTICE'), 'warning');
		}
		else
		{
			\JFactory::getApplication()->enqueueMessage(\JText::_('COM_INSTALLER_MSG_WARNINGS_NONE'), 'info');
		}
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		parent::addToolbar();

		ToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_WARNINGS');
	}
}
