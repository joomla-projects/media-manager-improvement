<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Config\Administrator\View\Component;

defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Config\Administrator\Helper\ConfigHelper;

/**
 * View for the component configuration
 *
 * @since  3.2
 */
class HtmlView extends BaseHtmlView
{
	public $state;

	public $form;

	public $component;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @see     \JViewLegacy::loadTemplate()
	 * @since   3.2
	 */
	public function display($tpl = null)
	{
		$form = null;
		$component = null;

		try
		{
			$component = $this->get('component');

			if (!$component->enabled)
			{
				return false;
			}

			$form = $this->get('form');
			$user = \JFactory::getUser();
		}
		catch (\Exception $e)
		{
			\JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		// Bind the form to the data.
		if ($form && $component->params)
		{
			$form->bind($component->params);
		}

		$this->fieldsets   = $form ? $form->getFieldsets() : null;
		$this->formControl = $form ? $form->getFormControl() : null;

		// Don't show permissions fieldset if not authorised.
		if (!$user->authorise('core.admin', $component->option) && isset($this->fieldsets['permissions']))
		{
			unset($this->fieldsets['permissions']);
		}

		$this->form = &$form;
		$this->component = &$component;

		$this->components = ConfigHelper::getComponentsWithConfig();

		$this->userIsSuperAdmin = $user->authorise('core.admin');
		$this->currentComponent = \JFactory::getApplication()->input->get('component');
		$this->return = \JFactory::getApplication()->input->get('return', '', 'base64');

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function addToolbar()
	{
		ToolbarHelper::title(\JText::_($this->component->option . '_configuration'), 'equalizer config');
		ToolbarHelper::saveGroup(
			[
				['apply', 'component.apply'],
				['save', 'component.save']
			],
			'btn-success'
		);
		ToolbarHelper::divider();
		ToolbarHelper::cancel('component.cancel');
		ToolbarHelper::divider();
		ToolbarHelper::help('JHELP_COMPONENTS_' . $this->currentComponent . '_OPTIONS');
	}
}
