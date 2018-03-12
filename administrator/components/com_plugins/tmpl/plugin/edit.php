<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$this->fieldsets = $this->form->getFieldsets('params');

$input = JFactory::getApplication()->input;

// In case of modal
$isModal  = $input->get('layout') === 'modal' ? true : false;
$layout   = $isModal ? 'modal' : 'edit';
$tmpl     = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

// Joomla4Upgrade TODO: Make work with J4 Modals
JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task) {
		if (task === 'plugin.cancel' || document.formvalidator.isValid(document.getElementById('style-form'))) {
			Joomla.submitform(task, document.getElementById('style-form'));
		}

		if (task !== 'plugin.apply') {
			if (self !== top) {
				window.top.setTimeout('window.parent.location = window.top.location.href', 1000);
				window.parent.jQuery('#plugin" . $this->item->extension_id . "Modal').modal('hide');
			}
		}
	};
");
?>

<form action="<?php echo JRoute::_('index.php?option=com_plugins&view=plugin&layout=' . $layout . $tmpl . '&extension_id=' . (int) $this->item->extension_id); ?>" method="post" name="adminForm" id="style-form" class="form-validate">
	<div>

		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_PLUGINS_PLUGIN')); ?>

		<div class="row">
			<div class="col-md-9">
				<?php if ($this->item->xml) : ?>
					<?php if ($this->item->xml->description) : ?>
						<h3>
							<?php
							if ($this->item->xml)
							{
								echo ($text = (string) $this->item->xml->name) ? JText::_($text) : $this->item->name;
							}
							else
							{
								echo JText::_('COM_PLUGINS_XML_ERR');
							}
							?>
						</h3>
						<div class="info-labels mb-1">
							<span class="badge badge-secondary">
								<?php echo $this->form->getValue('folder'); ?>
							</span> /
							<span class="badge badge-secondary">
								<?php echo $this->form->getValue('element'); ?>
							</span>
						</div>
						<div>
							<?php
							$this->fieldset    = 'description';
							$short_description = JText::_($this->item->xml->description);
							$this->fieldset    = 'description';
							$long_description  = JLayoutHelper::render('joomla.edit.fieldset', $this);

							if (!$long_description)
							{
								$truncated = JHtmlString::truncate($short_description, 550, true, false);

								if (strlen($truncated) > 500)
								{
									$long_description  = $short_description;
									$short_description = JHtmlString::truncate($truncated, 250);

									if ($short_description == $long_description)
									{
										$long_description = '';
									}
								}
							}
							?>
							<p><?php echo $short_description; ?></p>
							<?php if ($long_description) : ?>
								<p class="readmore">
									<a href="#" onclick="jQuery('.nav-tabs a[href=\'#description\']').tab('show');">
										<?php echo JText::_('JGLOBAL_SHOW_FULL_DESCRIPTION'); ?>
									</a>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<joomla-alert type="danger"><?php echo JText::_('COM_PLUGINS_XML_ERR'); ?></joomla-alert>
				<?php endif; ?>

				<?php
				$this->fieldset = 'basic';
				$html = JLayoutHelper::render('joomla.edit.fieldset', $this);
				echo $html ? '<hr>' . $html : '';
				?>
			</div>
			<div class="col-md-3">
				<div class="card card-light">
					<div class="card-body">
						<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
						<div class="form-vertical form-no-margin">
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('ordering'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('ordering'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('folder'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('folder'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('element'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('element'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if (isset($long_description) && $long_description != '') : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'description', JText::_('JGLOBAL_FIELDSET_DESCRIPTION')); ?>
			<?php echo $long_description; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php
		$this->fieldsets = array();
		$this->ignore_fieldsets = array('basic', 'description');
		echo JLayoutHelper::render('joomla.edit.params', $this);
		?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>

	<input type="hidden" name="task" value="">
	<?php echo JHtml::_('form.token'); ?>
</form>

