<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Event;

use Joomla\CMS\Application\CMSApplication;
use Joomla\DI\Container;

defined('JPATH_PLATFORM') or die;

/**
 * Event class for representing the extensions's `onBeforeExtensionBoot` event
 *
 * @since  4.0.0
 */
class BeforeExtensionBootEvent extends AbstractImmutableEvent
{
	/**
	 * Get the event's extension type. Can be:
	 * - component
	 *
	 * @return  string
	 *
	 * @since  4.0.0
	 */
	public function getExtensionType(): string
	{
		return $this->getArgument('type');
	}

	/**
	 * Get the event's extension name.
	 *
	 * @return  string
	 *
	 * @since  4.0.0
	 */
	public function getExtensionName(): string
	{
		return $this->getArgument('extensionName');
	}

	/**
	 * Get the event's container object
	 *
	 * @return  Container
	 *
	 * @since  4.0.0
	 */
	public function getContainer(): Container
	{
		return $this->getArgument('container');
	}
}
