<?php
/**
 * Part of the Joomla Framework DI Package
 *
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI;

use Psr\Container\ContainerExceptionInterface;

/**
 * Defines the interface for a Container Aware class.
 *
 * @since  1.0
 */
interface ContainerAwareInterface
{
	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @since   1.0
	 * @throws  ContainerExceptionInterface May be thrown if the container has not been set.
	 */
	public function getContainer();

	/**
	 * Set the DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	public function setContainer(Container $container);
}
