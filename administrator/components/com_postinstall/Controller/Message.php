<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_postinstall
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Postinstall\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Controller\Controller;
use Joomla\Component\Postinstall\Administrator\Helper\PostinstallHelper;
use Joomla\Component\Postinstall\Administrator\Model\Messages;

/**
 * Postinstall message controller.
 *
 * @since  3.2
 */
class Message extends Controller
{
	/**
	 * Resets all post-installation messages of the specified extension.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function reset()
	{
		/** @var Messages $model */
		$model = $this->getModel('Messages', '', array('ignore_request' => true));

		$eid = (int) $model->getState('eid', '700');

		if (empty($eid))
		{
			$eid = 700;
		}

		$model->resetMessages($eid);

		$this->setRedirect('index.php?option=com_postinstall&eid=' . $eid);
	}

	/**
	 * Unpublishes post-installation message of the specified extension.
	 *
	 * @return   void
	 *
	 * @since   3.2
	 */
	public function unpublish()
	{
		$model = $this->getModel('Messages', '', array('ignore_request' => true));

		$id = $this->input->get('id');

		$eid = (int) $model->getState('eid', '700');

		if (empty($eid))
		{
			$eid = 700;
		}

		$model->setState('published', 0);
		$model->unpublishMessage($id);

		$this->setRedirect('index.php?option=com_postinstall&eid=' . $eid);
	}

	/**
	 * Executes the action associated with an item.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function action()
	{
		$model = $this->getModel('Messages', '', array('ignore_request' => true));

		$id = $this->input->get('id');

		$item = $model->getItem($id);

		switch ($item->type)
		{
			case 'link':
				$this->setRedirect($item->action);

				return;

				break;

			case 'action':
				jimport('joomla.filesystem.file');

				$helper = new PostinstallHelper;
				$file = $helper->parsePath($item->action_file);

				if (\JFile::exists($file))
				{
					require_once $file;

					call_user_func($item->action);
				}
				break;

			case 'message':
			default:
				break;
		}

		$this->setRedirect('index.php?option=com_postinstall');
	}
}
