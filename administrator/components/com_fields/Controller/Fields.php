<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Fields\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Controller\Admin;

/**
 * Fields list controller class.
 *
 * @since  3.7.0
 */
class Fields extends Admin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 *
	 * @since   3.7.0
	 */
	protected $text_prefix = 'COM_FIELDS_FIELD';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  Array of configuration parameters.
	 *
	 * @return  \Joomla\CMS\Model\Model
	 *
	 * @since   3.7.0
	 */
	public function getModel($name = 'Field', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
