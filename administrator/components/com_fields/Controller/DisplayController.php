<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Fields\Administrator\Controller;

defined('_JEXEC') or die;

/**
 * Fields Controller
 *
 * @since  3.7.0
 */
class DisplayController extends \Joomla\CMS\MVC\Controller\BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 *
	 * @since   3.7.0
	 */
	protected $default_view = 'fields';

	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean     $cachable   If true, the view output will be cached
	 * @param   array|bool  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}
	 *
	 * @return Controller|boolean  A Controller object to support chaining.
	 *
	 * @since   3.7.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Set the default view name and format from the Request.
		$vName   = $this->input->get('view', 'fields');
		$id      = $this->input->getInt('id');

		// Check for edit form.
		if ($vName == 'field' && !$this->checkEditId('com_fields.edit.field', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(\JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(\JRoute::_('index.php?option=com_fields&view=fields&context=' . $this->input->get('context'), false));

			return false;
		}

		return parent::display($cachable, $urlparams);
	}
}
