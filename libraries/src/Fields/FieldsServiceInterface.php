<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Fields;

defined('JPATH_PLATFORM') or die;

/**
 * The fields service.
 *
 * @since  4.0.0
 */
interface FieldsServiceInterface
{
	/**
	 * Returns a valid section for the given section. If it is not valid then null
	 * is returned.
	 *
	 * @param   string  $section  The section to get the mapping for
	 * @param   object  $item     The item
	 *
	 * @return  string|null  The new section
	 *
	 * @since   4.0.0
	 */
	public function validateSection($section, $item = null);

	/**
	 * Returns valid contexts.
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	public function getContexts(): array;
}
