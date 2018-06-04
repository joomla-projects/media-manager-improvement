<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mailto
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

JHtml::_('behavior.core');
JHtml::_('behavior.keepalive');

Text::script('COM_MAILTO_EMAIL_ERR_NOINFO', true);

HTMLHelper::_('script', 'com_mailto/mailto-default.js', ['relative' => true, 'version' => 'auto']);

$data = $this->get('data');
?>

<div id="mailto-window" class="p-2">
	<h2>
		<?php echo JText::_('COM_MAILTO_EMAIL_TO_A_FRIEND'); ?>
	</h2>
	<div class="mailto-close">
		<a title="<?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?>" href="#" class="close-mailto">
		 <span>
             <?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?>
         </span></a>
	</div>

	<form action="<?php echo JUri::base() ?>index.php" id="mailtoForm" method="post">
		<div class="control-group">
			<div class="control-label">
				<label for="mailto_field">
                    <?php echo JText::_('COM_MAILTO_EMAIL_TO'); ?>
                </label>
			</div>
			<div class="controls">
				<input type="text" id="mailto_field" name="mailto" class="form-control" value="<?php echo $this->escape($data->mailto); ?>">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<label for="sender_field">
                    <?php echo JText::_('COM_MAILTO_SENDER'); ?>
                </label>
			</div>
			<div class="controls">
				<input type="text" id="sender_field" name="sender" class="form-control" value="<?php echo $this->escape($data->sender); ?>">
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<label for="from_field">
                    <?php echo JText::_('COM_MAILTO_YOUR_EMAIL'); ?>
                </label>
			</div>
			<div class="controls">
				<input type="text" id="from_field" name="from" class="form-control" value="<?php echo $this->escape($data->from); ?>">
			</div>
		</div>
		<div class="control-group">
				<div class="control-label">
			<label for="subject_field">
                <?php echo JText::_('COM_MAILTO_SUBJECT'); ?>
            </label>
			</div>
			<div class="controls">
				<input type="text" id="subject_field" name="subject" class="form-control" value="<?php echo $this->escape($data->subject); ?>">
			</div>
		</div>
		<div class="control-group">
			<button type="button" class="btn btn-secondary close-mailto">
				<?php echo JText::_('COM_MAILTO_CANCEL'); ?>
			</button>
			<button type="submit" class="btn btn-success">
				<?php echo JText::_('COM_MAILTO_SEND'); ?>
			</button>
		</div>

		<input type="hidden" name="layout" value="<?php echo htmlspecialchars($this->getLayout(), ENT_COMPAT, 'UTF-8'); ?>">
		<input type="hidden" name="option" value="com_mailto">
		<input type="hidden" name="task" value="send">
		<input type="hidden" name="tmpl" value="component">
		<input type="hidden" name="link" value="<?php echo $data->link; ?>">
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
