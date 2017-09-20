<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Users\Administrator\View\Level;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * View to edit a user view level.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The \JForm object
	 *
	 * @var  \JForm
	 */
	protected $form;

	/**
	 * The item data.
	 *
	 * @var   object
	 * @since 1.6
	 */
	protected $item;

	/**
	 * The model state.
	 *
	 * @var   \JObject
	 * @since 1.6
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

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
		\JFactory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);
		$canDo = ContentHelper::getActions('com_users');

		\JToolbarHelper::title(\JText::_($isNew ? 'COM_USERS_VIEW_NEW_LEVEL_TITLE' : 'COM_USERS_VIEW_EDIT_LEVEL_TITLE'), 'users levels-add');

		$toolbarButtons = [];

		if ($canDo->get('core.edit') || $canDo->get('core.create'))
		{
			$toolbarButtons[] = ['apply', 'level.apply'];
			$toolbarButtons[] = ['save', 'level.save'];
		}

		if ($canDo->get('core.create'))
		{
			$toolbarButtons[] = ['save2new', 'level.save2new'];
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			$toolbarButtons[] = ['save2copy', 'level.save2copy'];
		}

		\JToolbarHelper::saveGroup(
			$toolbarButtons,
			'btn-success'
		);

		if (empty($this->item->id))
		{
			\JToolbarHelper::cancel('level.cancel');
		}
		else
		{
			\JToolbarHelper::cancel('level.cancel', 'JTOOLBAR_CLOSE');
		}

		\JToolbarHelper::divider();
		\JToolbarHelper::help('JHELP_USERS_ACCESS_LEVELS_EDIT');
	}
}
