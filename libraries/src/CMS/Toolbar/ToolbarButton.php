<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Toolbar;

use Joomla\CMS\Layout\FileLayout;

defined('JPATH_PLATFORM') or die;

/**
 * Button base class
 *
 * The base class for all toolbar button types
 *
 * @since  3.0
 */
abstract class ToolbarButton
{
	/**
	 * element name
	 *
	 * This has to be set in the final renderer classes.
	 *
	 * @var    string
	 */
	protected $_name = null;

	/**
	 * reference to the object that instantiated the element
	 *
	 * @var    Toolbar
	 */
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @param   Toolbar  $parent  The parent
	 */
	public function __construct(Toolbar $parent = null)
	{
		$this->_parent = $parent;
	}

	/**
	 * Get the element name
	 *
	 * @return  string   type of the parameter
	 *
	 * @since   3.0
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Get the HTML to render the button
	 *
	 * @param   array  &$definition  Parameters to be passed
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public function render(&$definition)
	{
		/*
		 * Initialise some variables
		 */
		$action = call_user_func_array(array(&$this, 'fetchButton'), $definition);

		// Build the HTML Button
		$options = array();
		$options['action'] = $action;

		$layout = new FileLayout('joomla.toolbar.base');

		return $layout->render($options);
	}

	/**
	 * Method to get the CSS class name for an icon identifier
	 *
	 * Can be redefined in the final class
	 *
	 * @param   string  $identifier  Icon identification string
	 *
	 * @return  string  CSS class name
	 *
	 * @since   3.0
	 */
	public function fetchIconClass($identifier)
	{
		// It's an ugly hack, but this allows templates to define the icon classes for the toolbar
		$layout = new FileLayout('joomla.toolbar.iconclass');

		return $layout->render(array('icon' => $identifier));
	}

	/**
	 * Get the button
	 *
	 * Defined in the final button class
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	abstract public function fetchButton();
}
