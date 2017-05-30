<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Users\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Controller\Controller;

/**
 * Users mail controller.
 *
 * @since  1.6
 */
class Mail extends Controller
{
	/**
	 * Send the mail
	 *
	 * @return void
	 *
	 * @since 1.6
	 */
	public function send()
	{
		// Redirect to admin index if mass mailer disabled in conf
		if ($this->app->get('massmailoff', 0) == 1)
		{
			$this->app->redirect(\JRoute::_('index.php', false));
		}

		// Check for request forgeries.
		\JSession::checkToken('request') or jexit(\JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('Mail');

		if ($model->send())
		{
			$type = 'message';
		}
		else
		{
			$type = 'error';
		}

		$msg = $model->getError();
		$this->setRedirect('index.php?option=com_users&view=mail', $msg, $type);
	}

	/**
	 * Cancel the mail
	 *
	 * @return void
	 *
	 * @since 1.6
	 */
	public function cancel()
	{
		// Check for request forgeries.
		\JSession::checkToken('request') or jexit(\JText::_('JINVALID_TOKEN'));
		$this->setRedirect('index.php');
	}
}
