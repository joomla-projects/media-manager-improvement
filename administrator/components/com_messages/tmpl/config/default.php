<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>
<div class="container-popup">
	<form action="<?php echo Route::_('index.php?option=com_messages&view=config'); ?>" method="post" name="adminForm" id="message-form">
		<fieldset>
			<?php echo $this->form->renderField('lock'); ?>
			<?php echo $this->form->renderField('mail_on_new'); ?>
			<?php echo $this->form->renderField('auto_purge'); ?>
		</fieldset>
		<button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitform('config.save', this.form);"></button>

		<input type="hidden" name="task" value="">
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
