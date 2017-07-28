<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Content\Administrator\View\Featured;

defined('_JEXEC') or die;

use Joomla\CMS\View\HtmlView;
use Joomla\Component\Content\Administrator\Helper\ContentHelper;

/**
 * View class for a list of featured articles.
 *
 * @since  1.6
 */
class Html extends HtmlView
{
	/**
	 * List of authors. Each stdClass has two properties - value and text, containing the user id and user's name
	 * respectively
	 *
	 * @var  \stdClass
	 */
	protected $authors;

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
	 * Array used for displaying the levels filter
	 *
	 * @return  stdClass[]
	 * @since  __DEPLOY_VERSION__
	 */
	protected $f_levels;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		ContentHelper::addSubmenu('featured');

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->authors       = $this->get('Authors');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->vote          = \JPluginHelper::isEnabled('content', 'vote');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		$this->addToolbar();
		$this->sidebar = \JHtmlSidebar::render();

		return parent::display($tpl);
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
		$state = $this->get('State');
		$canDo = \JHelperContent::getActions('com_content', 'category', $this->state->get('filter.category_id'));

		\JToolbarHelper::title(\JText::_('COM_CONTENT_FEATURED_TITLE'), 'star featured');

		if ($canDo->get('core.create'))
		{
			\JToolbarHelper::addNew('article.add');
		}

		if ($canDo->get('core.edit.state'))
		{
			\JToolbarHelper::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
			\JToolbarHelper::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			\JToolbarHelper::custom('articles.unfeatured', 'unfeatured.png', 'featured_f2.png', 'JUNFEATURE', true);
			\JToolbarHelper::archiveList('articles.archive');
			\JToolbarHelper::checkin('articles.checkin');
		}

		if ($state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			\JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			\JToolbarHelper::trash('articles.trash');
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			\JToolbarHelper::preferences('com_content');
		}

		\JToolbarHelper::help('JHELP_CONTENT_FEATURED_ARTICLES');
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'fp.ordering'    => \JText::_('JGRID_HEADING_ORDERING'),
			'a.state'        => \JText::_('JSTATUS'),
			'a.title'        => \JText::_('JGLOBAL_TITLE'),
			'category_title' => \JText::_('JCATEGORY'),
			'access_level'   => \JText::_('JGRID_HEADING_ACCESS'),
			'a.created_by'   => \JText::_('JAUTHOR'),
			'language'       => \JText::_('JGRID_HEADING_LANGUAGE'),
			'a.created'      => \JText::_('JDATE'),
			'a.id'           => \JText::_('JGRID_HEADING_ID'),
		);
	}
}
