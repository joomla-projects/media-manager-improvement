<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Banners\Administrator\Field;

defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;

/**
 * Impressions field.
 *
 * @since  1.6
 */
class ImpmadeField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $type = 'ImpMade';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		$onclick = ' onclick="document.getElementById(\'' . $this->id . '\').value=\'0\';"';

		return '<div class="input-group"><input class="form-control" type="text" name="' . $this->name . '" id="' . $this->id . '" value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly">'
			. '<span class="input-group-append"><a class="btn btn-secondary" ' . $onclick . '>'
			. '<span class="icon-refresh" aria-hidden="true"></span> ' . \JText::_('COM_BANNERS_RESET_IMPMADE') . '</a></span></div>';
	}
}
