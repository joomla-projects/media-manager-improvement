<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Banners\Administrator\View\Tracks;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Banners\Administrator\Helper\BannersHelper;

/**
 * View class for a list of tracks.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
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
	 * @var  object
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		BannersHelper::addSubmenu('tracks');

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
		$canDo = \JHelperContent::getActions('com_banners', 'category', $this->state->get('filter.category_id'));

		\JToolbarHelper::title(\JText::_('COM_BANNERS_MANAGER_TRACKS'), 'bookmark banners-tracks');

		$bar = \JToolbar::getInstance('toolbar');

		// Instantiate a new \JLayoutFile instance and render the export button
		$layout = new \JLayoutFile('joomla.toolbar.modal');

		$dhtml  = $layout->render(
			array(
				'selector' => 'downloadModal',
				'icon'     => 'download',
				'text'     => \JText::_('JTOOLBAR_EXPORT'),
				'doTask'   => \JRoute::_('index.php?option=com_banners&view=download&tmpl=component'),
			)
		);

		$bar->appendButton('Custom', $dhtml, 'download');

		if ($canDo->get('core.delete'))
		{
			$bar->appendButton('Confirm', 'COM_BANNERS_DELETE_MSG', 'delete', 'COM_BANNERS_TRACKS_DELETE', 'tracks.delete', false);
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			\JToolbarHelper::preferences('com_banners');
		}

		\JToolbarHelper::help('JHELP_COMPONENTS_BANNERS_TRACKS');

		\JHtmlSidebar::setAction('index.php?option=com_banners&view=tracks');
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
			'b.name'     => \JText::_('COM_BANNERS_HEADING_NAME'),
			'cl.name'    => \JText::_('COM_BANNERS_HEADING_CLIENT'),
			'track_type' => \JText::_('COM_BANNERS_HEADING_TYPE'),
			'count'      => \JText::_('COM_BANNERS_HEADING_COUNT'),
			'track_date' => \JText::_('JDATE')
		);
	}
}
