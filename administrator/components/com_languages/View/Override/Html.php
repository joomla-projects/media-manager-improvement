<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Languages\Administrator\View\Override;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\View\HtmlView;

/**
 * View to edit an language override
 *
 * @since  2.5
 */
class Html extends HtmlView
{
	/**
	 * The form to use for the view.
	 *
	 * @var		object
	 * @since	2.5
	 */
	protected $form;

	/**
	 * The item to edit.
	 *
	 * @var		object
	 * @since	2.5
	 */
	protected $item;

	/**
	 * The model state.
	 *
	 * @var		object
	 * @since	2.5
	 */
	protected $state;

	/**
	 * Displays the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors));
		}

		// Check whether the cache has to be refreshed.
		$cached_time = \JFactory::getApplication()->getUserState(
			'com_languages.overrides.cachedtime.' . $this->state->get('filter.client') . '.' . $this->state->get('filter.language'),
			0
		);

		if (time() - $cached_time > 60 * 5)
		{
			$this->state->set('cache_expired', true);
		}

		// Add strings for translations in \Javascript.
		\JText::script('COM_LANGUAGES_VIEW_OVERRIDE_NO_RESULTS');
		\JText::script('COM_LANGUAGES_VIEW_OVERRIDE_REQUEST_ERROR');

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Adds the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since	2.5
	 */
	protected function addToolbar()
	{
		\JFactory::getApplication()->input->set('hidemainmenu', true);

		$canDo = ContentHelper::getActions('com_languages');

		\JToolbarHelper::title(\JText::_('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_TITLE'), 'comments-2 langmanager');

		$toolbarButtons = [];

		if ($canDo->get('core.edit'))
		{
			$toolbarButtons[] = ['apply', 'override.apply'];
			$toolbarButtons[] = ['save', 'override.save'];
		}

		// This component does not support Save as Copy.
		if ($canDo->get('core.edit') && $canDo->get('core.create'))
		{
			$toolbarButtons[] = ['save2new', 'override.save2new'];
		}

		\JToolbarHelper::saveGroup(
			$toolbarButtons,
			'btn-success'
		);

		if (empty($this->item->key))
		{
			\JToolbarHelper::cancel('override.cancel');
		}
		else
		{
			\JToolbarHelper::cancel('override.cancel', 'JTOOLBAR_CLOSE');
		}

		\JToolbarHelper::divider();
		\JToolbarHelper::help('JHELP_EXTENSIONS_LANGUAGE_MANAGER_OVERRIDES_EDIT');
	}
}
