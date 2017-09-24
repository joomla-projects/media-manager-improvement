<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Users\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Reset controller class for Users.
 *
 * @since  1.6
 */
class RemindController extends BaseController
{
	/**
	 * Method to request a username reminder.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function remind()
	{
		// Check the request token.
		$this->checkToken('post');

		/* @var \Joomla\Component\Users\Site\Model\RemindModel $model */
		$model = $this->getModel('Remind', 'Site');
		$data  = $this->input->post->get('jform', array(), 'array');

		// Submit the password reset request.
		$return	= $model->processRemindRequest($data);

		// Check for a hard error.
		if ($return == false)
		{
			// The request failed.
			// Go back to the request form.
			$message = \JText::sprintf('COM_USERS_REMIND_REQUEST_FAILED', $model->getError());
			$this->setRedirect(\JRoute::_('index.php?option=com_users&view=remind', false), $message, 'notice');

			return false;
		}
		else
		{
			// The request succeeded.
			// Proceed to step two.
			$message = \JText::_('COM_USERS_REMIND_REQUEST_SUCCESS');
			$this->setRedirect(\JRoute::_('index.php?option=com_users&view=login', false), $message);

			return true;
		}
	}
}
