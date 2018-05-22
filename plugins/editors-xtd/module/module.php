<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.module
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Object\CMSObject;

/**
 * Editor Module button
 *
 * @since  3.5
 */
class PlgButtonModule extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.5
	 */
	protected $autoloadLanguage = true;

	/**
	 * Display the button
	 *
	 * @param   string  $name  The name of the button to add
	 *
	 * @return  CMSObject  The button options as JObject
	 *
	 * @since   3.5
	 */
	public function onDisplay($name)
	{
		/*
		 * Use the built-in element view to select the module.
		 * Currently uses blank class.
		 */
		$user  = Factory::getUser();

		if ($user->authorise('core.create', 'com_modules')
			|| $user->authorise('core.edit', 'com_modules')
			|| $user->authorise('core.edit.own', 'com_modules'))
		{
			$link = 'index.php?option=com_modules&amp;view=modules&amp;layout=modal&amp;tmpl=component&amp;editor='
					. $name . '&amp;' . Session::getFormToken() . '=1';
			$button          = new CMSObject;
			$button->modal   = true;
			$button->link    = $link;
			$button->text    = Text::_('PLG_MODULE_BUTTON_MODULE');
			$button->name    = 'file-add';
			$button->options = [
				'height'     => '300px',
				'width'      => '800px',
				'bodyHeight' => '70',
				'modalWidth' => '80',
			];

			return $button;
		}
	}
}
