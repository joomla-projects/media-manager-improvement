<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for cloaking email addresses
 *
 * @since  1.5
 */
abstract class JHtmlEmail
{
	/**
	 * Simple JavaScript email cloaker
	 *
	 * By default replaces an email with a mailto link with email cloaked
	 *
	 * @param   string   $mail           The -mail address to cloak.
	 * @param   boolean  $mailto         True if text and mailing address differ
	 * @param   string   $text           Text for the link
	 * @param   boolean  $email          True if text is an email address
	 * @param   string   $attribsBefore  Any attributes before the email address
	 * @param   string   $attribsAfter   Any attributes before the email address
	 *
	 * @return  string  The cloaked email.
	 *
	 * @since   1.5
	 */
	public static function cloak($mail, $mailto = true, $text = '', $email = true, $attribsBefore = '', $attribsAfter = '')
	{
		// Handle IDN addresses: punycode for href but utf-8 for text displayed.
		if ($mailto && (empty($text) || $email))
		{
			// Use dedicated $text whereas $mail is used as href and must be punycoded.
			$text = PunycodeHelper::emailToUTF8($text ?: $mail);
		}
		elseif (!$mailto)
		{
			// In that case we don't use link - so convert $mail back to utf-8.
			$mail = PunycodeHelper::emailToUTF8($mail);
		}

		// Split email by @ symbol
		$mail   = explode('@', $mail);
		$name   = @$mail[0];
		$domain = @$mail[1];

		// Include the email cloaking script
		HTMLHelper::_('webcomponent', 'system/webcomponents/joomla-hidden-mail.js', ['version' => 'auto', 'relative' => true]);

		return '<joomla-hidden-mail '
			. $attribsBefore . ' is-link="'
			. $mailto . '" is-email="'
			. $email . '" first="'
			. base64_encode($name) . '" last="'
			. base64_encode($domain) . '" text="'
			. base64_encode($text) . '" base="'
			. Uri::root(true) . '" ' . $attribsAfter . '>' . Text::_('JLIB_HTML_CLOAKING') . '</joomla-hidden-mail>';
	}
}
