<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('form.csrf');
HTMLHelper::_('webcomponent', 'system/webcomponents/joomla-field-send-test-mail.min.js', ['version' => 'auto', 'relative' => true]);

// Load JavaScript message titles
Text::script('ERROR');
Text::script('WARNING');
Text::script('NOTICE');
Text::script('MESSAGE');

// Add strings for JavaScript error translations.
Text::script('JLIB_JS_AJAX_ERROR_CONNECTION_ABORT');
Text::script('JLIB_JS_AJAX_ERROR_NO_CONTENT');
Text::script('JLIB_JS_AJAX_ERROR_OTHER');
Text::script('JLIB_JS_AJAX_ERROR_PARSE');
Text::script('JLIB_JS_AJAX_ERROR_TIMEOUT');

// Ajax request data.
$ajaxUri = Route::_('index.php?option=com_config&task=application.sendtestmail&format=json&' . Session::getFormToken() . '=1');

$this->name = Text::_('COM_CONFIG_MAIL_SETTINGS');
$this->fieldsname = 'mail';
?>

<joomla-field-send-test-mail uri="<?php echo $ajaxUri; ?>">
	<?php echo LayoutHelper::render('joomla.content.options_default', $this); ?>

	<button class="btn btn-primary" type="button" id="sendtestmail">
		<span><?php echo Text::_('COM_CONFIG_SENDMAIL_ACTION_BUTTON'); ?></span>
	</button>
</joomla-field-send-test-mail>
