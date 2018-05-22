<?php
/**
 * @package     Joomla.Cms
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\MVC\View;

defined('JPATH_PLATFORM') or die;

/**
 * Base class for a Joomla List View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  2.5.5
 */
class ListView extends HtmlView
{
	/**
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var  \JPagination
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var  \JObject
	 */
	protected $state;

	/**
	 * The actions the user is authorised to perform
	 *
	 * @var  \JObject
	 */
	protected $canDo;

	/**
	 * Form object for search filters
	 *
	 * @var  \JForm
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var  array
	 */
	public $activeFilters;

	/**
	 * The sidebar markup
	 *
	 * @var  string
	 */
	protected $sidebar;

	/**
	 * The toolbar title
	 *
	 * @var string
	 */
	protected $toolbarTitle;

	/**
	 * The toolbar icon
	 *
	 * @var string
	 */
	protected $toolbarIcon;

	/**
	 * The flag which determine whether we want to show batch button
	 *
	 * @var bool
	 */
	protected $supportsBatch = true;

	/**
	 * The help link for the view
	 *
	 * @var string
	 */
	protected $helpLink;

	/**
	 * Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(array $config)
	{
		parent::__construct($config);

		// Set class properties from config data passed in constructor
		if (isset($config['toolbar_title']))
		{
			$this->toolbarTitle = $config['toolbar_title'];
		}
		else
		{
			$this->toolbarTitle = strtoupper($this->option . '_MANAGER_' . $this->getName());
		}

		if (isset($config['toolbar_icon']))
		{
			$this->toolbarIcon = $config['toolbar_icon'];
		}
		else
		{
			$this->toolbarIcon = strtolower($this->getName());
		}

		if (isset($config['supports_batch']))
		{
			$this->supportsBatch = $config['supports_batch'];
		}

		if (isset($config['help_link']))
		{
			$this->helpLink = $config['help_link'];
		}

		// Set default value for $canDo to avoid fatal error if child class doesn't set value for this property
		$this->canDo = new \JObject;
	}

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		// Prepare view data
		$this->initializeView();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		// Build toolbar
		$this->addToolbar();

		// Render the view
		return parent::display($tpl);
	}

	/**
	 * Prepare view data
	 *
	 * @return  void
	 */
	protected function initializeView()
	{
		$componentName = substr($this->option, 4);
		$helperClass = ucfirst($componentName . 'Helper');

		// Include the component helpers.
		\JLoader::register($helperClass, JPATH_COMPONENT . '/helpers/' . $componentName . '.php');
		\JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		if ($this->getLayout() !== 'modal')
		{
			if (is_callable($helperClass . '::addSubmenu'))
			{
				call_user_func(array($helperClass, 'addSubmenu'), $this->getName());
			}

			$this->sidebar = \JHtmlSidebar::render();
		}

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
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
		$canDo = $this->canDo;
		$user  = \JFactory::getUser();

		// Get the toolbar object instance
		$bar = \JToolbar::getInstance('toolbar');

		$viewName = $this->getName();
		$singularViewName = \Joomla\String\Inflector::getInstance()->toSingular($viewName);

		\JToolbarHelper::title(\JText::_($this->toolbarTitle), $this->toolbarIcon);

		if ($canDo->get('core.create'))
		{
			\JToolbarHelper::addNew($singularViewName . '.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			\JToolbarHelper::editList($singularViewName . '.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			\JToolbarHelper::publish($viewName . '.publish', 'JTOOLBAR_PUBLISH', true);
			\JToolbarHelper::unpublish($viewName . '.unpublish', 'JTOOLBAR_UNPUBLISH', true);

			if (isset($this->items[0]->featured))
			{
				\JToolbarHelper::custom($viewName . '.featured', 'featured.png', 'featured_f2.png', 'JFEATURE', true);
				\JToolbarHelper::custom($viewName . '.unfeatured', 'unfeatured.png', 'featured_f2.png', 'JUNFEATURE', true);
			}

			\JToolbarHelper::archiveList($viewName . '.archive');
			\JToolbarHelper::checkin($viewName . '.checkin');
		}

		// Add a batch button
		if ($this->supportsBatch && $user->authorise('core.create', $this->option)
			&& $user->authorise('core.edit', $this->option)
			&& $user->authorise('core.edit.state', $this->option))
		{
			$title = \JText::_('JTOOLBAR_BATCH');

			// Instantiate a new \JLayoutFile instance and render the batch button
			$layout = new \JLayoutFile('joomla.toolbar.batch');

			$dhtml = $layout->render(array('title' => $title));
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			\JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', $viewName . '.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			\JToolbarHelper::trash($viewName . '.trash');
		}

		if ($user->authorise('core.admin', $this->option) || $user->authorise('core.options', $this->option))
		{
			\JToolbarHelper::preferences($this->option);
		}

		if ($this->helpLink)
		{
			\JToolbarHelper::help($this->helpLink);
		}
	}
}
