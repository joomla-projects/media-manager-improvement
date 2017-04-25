<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_postinstall
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Postinstall\Administrator\View\Messages;

defined('_JEXEC') or die;

use Joomla\CMS\View\HtmlView;
use Joomla\Component\Postinstall\Administrator\Model\Messages;

/**
 * Model class to display postinstall messages
 *
 * @since  3.2
 */
class Html extends HtmlView
{
	/**
	 * Executes before rendering the page for the Browse task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 *
	 * @since   3.2
	 */
	public function display($tpl = null)
	{
		/** @var Messages $model */
		$model = $this->getModel();

		$this->items = $model->getItems();

		$this->eid = (int) $model->getState('eid', '700', 'int');

		if (empty($this->eid))
		{
			$this->eid = 700;
		}

		$this->toolbar();

		$this->token = \JFactory::getSession()->getFormToken();
		$this->extension_options = $model->getComponentOptions();

		\JToolbarHelper::title(\JText::sprintf('COM_POSTINSTALL_MESSAGES_TITLE', $model->getExtensionName($this->eid)));

		return parent::display($tpl);
	}

	/**
	 * displays the toolbar
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	private function toolbar()
	{
		// Options button.
		if (\JFactory::getUser()->authorise('core.admin', 'com_postinstall'))
		{
			\JToolbarHelper::preferences('com_postinstall', 550, 875);
			\JToolbarHelper::help('JHELP_COMPONENTS_POST_INSTALLATION_MESSAGES');
		}
	}
}
