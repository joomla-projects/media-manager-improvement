<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Adapter;

defined('_JEXEC') or die;

interface OAuthCallbackHandlerInterface
{
	public function onCallback($context = null, $params = null);
}