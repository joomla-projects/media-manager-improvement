<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Banners\Administrator\Field;

defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormHelper;

\JLoader::register('BannersHelper', JPATH_ADMINISTRATOR . '/components/com_banners/helpers/banners.php');

FormHelper::loadFieldClass('list');

/**
 * Bannerclient field.
 *
 * @since  1.6
 */
class BannerclientField extends \JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'BannerClient';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	public function getOptions()
	{
		return array_merge(parent::getOptions(), \BannersHelper::getClientOptions());
	}
}
