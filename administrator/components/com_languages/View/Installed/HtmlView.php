<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Languages\Administrator\View\Installed;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Languages\Administrator\Helper\LanguagesHelper;

/**
 * Displays a list of the installed languages.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * True, if FTP settings should be shown, or an exception.
	 *
	 * @var boolean|\JException
	 */
	protected $ftp = null;

	/**
	 * Option (component) name
	 *
	 * @var string
	 */
	protected $option = null;

	/**
	 * The pagination object
	 *
	 * @var  \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * Languages information
	 *
	 * @var array
	 */
	protected $rows = null;

	/**
	 * The model state
	 *
	 * @var    \JObject
	 * @since  4.0.0
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var    \JForm
	 * @since  4.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	public $activeFilters;

	/**
	 * The sidebar markup
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $sidebar;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->ftp           = $this->get('Ftp');
		$this->option        = $this->get('Option');
		$this->pagination    = $this->get('Pagination');
		$this->rows          = $this->get('Data');
		$this->total         = $this->get('Total');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		LanguagesHelper::addSubmenu('installed');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
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
		$canDo = ContentHelper::getActions('com_languages');

		if ((int) $this->state->get('client_id') === 1)
		{
			\JToolbarHelper::title(\JText::_('COM_LANGUAGES_VIEW_INSTALLED_ADMIN_TITLE'), 'comments-2 langmanager');
		}
		else
		{
			\JToolbarHelper::title(\JText::_('COM_LANGUAGES_VIEW_INSTALLED_SITE_TITLE'), 'comments-2 langmanager');
		}

		if ($canDo->get('core.edit.state'))
		{
			\JToolbarHelper::makeDefault('installed.setDefault');
			\JToolbarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			// Add install languages link to the lang installer component.
			$bar = \JToolbar::getInstance('toolbar');

			// Switch administrator language
			if ($this->state->get('client_id', 0) == 1)
			{
				\JToolbarHelper::custom('installed.switchadminlanguage', 'refresh', 'refresh', 'COM_LANGUAGES_SWITCH_ADMIN', false);
				\JToolbarHelper::divider();
			}

			$bar->appendButton('Link', 'upload', 'COM_LANGUAGES_INSTALL', 'index.php?option=com_installer&view=languages');
			\JToolbarHelper::divider();

			\JToolbarHelper::preferences('com_languages');
			\JToolbarHelper::divider();
		}

		\JToolbarHelper::help('JHELP_EXTENSIONS_LANGUAGE_MANAGER_INSTALLED');

		$this->sidebar = \JHtmlSidebar::render();
	}
}
