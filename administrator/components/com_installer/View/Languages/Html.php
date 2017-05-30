<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Installer\Administrator\View\Languages;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Installer\Administrator\View\Installer\Html as InstallerViewDefault;

/**
 * Language installer view
 *
 * @since  2.5.7
 */
class Html extends InstallerViewDefault
{
	/**
	 * @var object item list
	 */
	protected $items;

	/**
	 * @var object pagination information
	 */
	protected $pagination;

	/**
	 * Display the view.
	 *
	 * @param   null  $tpl  template to display
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		// Get data from the model.
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->installedLang = LanguageHelper::getInstalledLanguages();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 */
	protected function addToolbar()
	{
		$canDo = ContentHelper::getActions('com_installer');
		ToolbarHelper::title(\JText::_('COM_INSTALLER_HEADER_' . $this->getName()), 'puzzle install');

		if ($canDo->get('core.admin'))
		{
			parent::addToolbar();

			// TODO: this help screen will need to be created.
			ToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_LANGUAGES');
		}
	}
}
