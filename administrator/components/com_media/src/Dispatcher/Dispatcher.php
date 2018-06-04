<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;

/**
 * Dispatcher class for com_media
 *
 * @since  4.0.0
 */
class Dispatcher extends \Joomla\CMS\Dispatcher\Dispatcher
{
	/**
	 * Method to check component access permission
	 *
	 * @since   4.0.0
	 *
	 * @return  void
	 */
	protected function checkAccess()
	{
		$user   = $this->app->getIdentity();
		$asset  = $this->input->get('asset');
		$author = $this->input->get('author');

		// Access check
		if (!$user->authorise('core.manage', 'com_media')
			&& (!$asset || (!$user->authorise('core.edit', $asset)
			&& !$user->authorise('core.create', $asset)
			&& count($user->getAuthorisedCategories($asset, 'core.create')) == 0)
			&& !($user->id == $author && $user->authorise('core.edit.own', $asset))))
		{
			throw new NotAllowed($this->app->getLanguage()->_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}
}
