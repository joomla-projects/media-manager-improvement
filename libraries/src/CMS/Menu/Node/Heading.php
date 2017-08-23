<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\CMS\Menu\Node;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Menu\Node;

/**
 * A Heading type of node for MenuTree
 *
 * @see    Node
 *
 * @since  __DEPLOY_VERSION__
 */
class Heading extends Node
{
	/**
	 * Node Title
	 *
	 * @var  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected $title = null;

	/**
	 * Node Link
	 *
	 * @var  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected $link = '#';

	/**
	 * Link title icon
	 *
	 * @var  string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected $icon = null;

	/**
	 * Constructor for the class.
	 *
	 * @param   string  $title  The title of the node
	 * @param   string  $class  The CSS class for the node
	 * @param   string  $id     The node id
	 * @param   string  $icon   The title icon for the node
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct($title, $class = null, $id = null, $icon = null)
	{
		$this->title = $title;
		$this->class = $class;
		$this->id    = $id;
		$this->icon  = $icon;

		parent::__construct();
	}

	/**
	 * Get an attribute value
	 *
	 * @param   string  $name  The attribute name
	 *
	 * @return  mixed
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function get($name)
	{
		switch ($name)
		{
			case 'title':
			case 'link':
			case 'icon':
				return $this->$name;
		}

		return parent::get($name);
	}
}
