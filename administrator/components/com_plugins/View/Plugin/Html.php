<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Plugins\Administrator\View\Plugin;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\View\HtmlView;

/**
 * View to edit a plugin.
 *
 * @since  1.5
 */
class Html extends HtmlView
{
	/**
	 * The item object for the newsfeed
	 *
	 * @var    \JObject
	 */
	protected $item;

	/**
	 * The form object for the newsfeed
	 *
	 * @var    \JForm
	 */
	protected $form;

	/**
	 * The model state of the newsfeed
	 *
	 * @var    \JObject
	 */
	protected $state;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

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

		$canDo = ContentHelper::getActions('com_plugins');

		\JToolbarHelper::title(\JText::sprintf('COM_PLUGINS_MANAGER_PLUGIN', \JText::_($this->item->name)), 'power-cord plugin');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit'))
		{
			\JToolbarHelper::saveGroup(
				[
					['apply', 'plugin.apply'],
					['save', 'plugin.save']
				],
				'btn-success'
			);
		}

		\JToolbarHelper::cancel('plugin.cancel', 'JTOOLBAR_CLOSE');
		\JToolbarHelper::divider();

		// Get the help information for the plugin item.
		$lang = \JFactory::getLanguage();

		$help = $this->get('Help');

		if ($lang->hasKey($help->url))
		{
			$debug = $lang->setDebug(false);
			$url = \JText::_($help->url);
			$lang->setDebug($debug);
		}
		else
		{
			$url = null;
		}

		\JToolbarHelper::help($help->key, false, $url);
	}
}
