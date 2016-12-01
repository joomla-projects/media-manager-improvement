<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
JHtml::_('formbehavior.chosen', 'select');

$this->configFieldsets  = array('editorConfig');
$this->hiddenFieldsets  = array('basic-limited');
$this->ignore_fieldsets = array('jmetadata', 'item_associations');

// Create shortcut to parameters.
$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;

$assoc = JLanguageAssociations::isEnabled();

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$params = json_decode($params);
$editoroptions = isset($params->show_publishing_options);

if (!$editoroptions)
{
	$params->show_publishing_options = '1';
	$params->show_file_options = '1';
	$params->show_urls_images_backend = '0';
	$params->show_urls_images_frontend = '0';
}

// Check if the file uses configuration settings besides global. If so, use them.
if (isset($this->item->attribs['show_publishing_options']) && $this->item->attribs['show_publishing_options'] != '')
{
	$params->show_publishing_options = $this->item->attribs['show_publishing_options'];
}

if (isset($this->item->attribs['show_file_options']) && $this->item->attribs['show_file_options'] != '')
{
	$params->show_file_options = $this->item->attribs['show_file_options'];
}

if (isset($this->item->attribs['show_urls_images_frontend']) && $this->item->attribs['show_urls_images_frontend'] != '')
{
	$params->show_urls_images_frontend = $this->item->attribs['show_urls_images_frontend'];
}

if (isset($this->item->attribs['show_urls_images_backend']) && $this->item->attribs['show_urls_images_backend'] != '')
{
	$params->show_urls_images_backend = $this->item->attribs['show_urls_images_backend'];
}

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "file.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			jQuery("#permissions-sliders select").attr("disabled", "disabled");
			Joomla.submitform(task, document.getElementById("item-form"));

			// @deprecated 4.0  The following js is not needed since __DEPLOY_VERSION__.
			if (task !== "file.apply")
			{
				window.parent.jQuery("#fileEdit' . (int) $this->item->id . 'Modal").modal("hide");
			}
		}
	};
');

// In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
?>

<form action="<?php echo JRoute::_('index.php?option=com_media&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_MEDIA_FILE_CONTENT')); ?>
		<?php echo $this->form->renderField('filename'); ?>
		<?php echo $this->form->renderField('path'); ?>
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<div class="container">
						<div class="row-fluid">
							<div class="span1">
								<ul id="myTab" class="nav nav-pills">
									<li class="active">
										<a href="#dimensions" data-toggle="tab" class="hasTooltip" data-placement="right" title="Dimensions">
											<span class="icon-flag"></span>
										</a>
									</li>
									<li class="">
										<a href="#filter" data-toggle="tab" class="hasTooltip" data-placement="right" title="Filter">
											<span class="icon-pencil-2"></span>
										</a>
									</li>
									<li class="">
										<a href="#effects" data-toggle="tab" class="hasTooltip" data-placement="right" title="Effects">
											<span class="icon-home"></span>
										</a>
									</li>
									<li class="">
										<a href="#presets" data-toggle="tab" class="hasTooltip" data-placement="right" title="Presets">
											<span class="icon-tag"></span>
										</a>
									</li>
								</ul>
							</div>
							<div class="span3">
								<div id="myTabContent" class="tab-content">
									<div class="tab-pane fade active in" id="dimensions">
										<table class="table">
											<thead>
											<tbody>
											<tr>
												<td>
													<p>
														<input class="span4" type="text" placeholder="800px"> x
														<input class="span4" type="text" placeholder="600px">
													<p>

														<select>
															<option>No Preset</option>
															<option>Crop1x1 and Greyscale</option>
															<option>Brightness 50</option>
														</select>

												</td>
											</tr>

											<tr>
												<td>
													<p>
														<input class="span4" type="text" placeholder="2800px"> x
														<input class="span4" type="text" placeholder="2600px">
													<p>

														<select>
															<option>No Preset</option>
															<option>Crop1x1 and Greyscale</option>
															<option>Brightness 50</option>
														</select>

												</td>
											</tr>

											<tr>
												<td>
													<p>
														<input class="span4" type="text" placeholder="3800px"> x
														<input class="span4" type="text" placeholder="3600px">
													<p>

														<select>
															<option>No Preset</option>
															<option>Crop1x1 and Greyscale</option>
															<option>Brightness 50</option>
														</select>

												</td>
											</tr>


											</tbody>
										</table>

										<button class="btn" type="button"><i class="icon-white icon-plus"></i></button>
										<button class="btn" type="button"><i class="icon-white icon-minus"></i></button>

									</div>
									<div class="tab-pane fade" id="filter">
										<div class="accordion" id="accordion2">
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
														<i class="icon-move"></i> Crop
													</a>
												</div>
												<div id="collapseOne" class="accordion-body collapse in">
													<div class="accordion-inner">
														<p><input type="text" placeholder="Type something…"></p>
														<p>
															<button class="btn" type="button"><i class="icon-white icon-ok"></i></button>
															<button class="btn" type="button"><i class="icon-white icon-remove"></i></button>
														</p>
													</div>
												</div>
											</div>
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
														<i class="icon-move"></i> Resize
													</a>
												</div>
												<div id="collapseTwo" class="accordion-body collapse">
													<div class="accordion-inner">
														<p><input type="text" placeholder="Type something…"></p>
														<p>
															<button class="btn" type="button"><i class="icon-white icon-ok"></i></button>
															<button class="btn" type="button"><i class="icon-white icon-remove"></i></button>
														</p>
													</div>
												</div>
											</div>
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
														<i class="icon-move"></i> Rotate
													</a>
												</div>
												<div id="collapseThree" class="accordion-body collapse">
													<div class="accordion-inner">
														<p><input type="text" placeholder="Type something…"></p>
														<p>
															<button class="btn" type="button"><i class="icon-white icon-ok"></i></button>
															<button class="btn" type="button"><i class="icon-white icon-remove"></i></button>
														</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="effects">
										<div class="accordion" id="accordion3">
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#collapseOne">
														<i class="icon-move"></i> Contrast
													</a>
												</div>
												<div id="collapseOne" class="accordion-body collapse in">
													<div class="accordion-inner">
														<p><input type="text" placeholder="Type something…"></p>
														<p>
															<button class="btn" type="button"><i class="icon-white icon-ok"></i></button>
															<button class="btn" type="button"><i class="icon-white icon-remove"></i></button>
														</p>
													</div>
												</div>
											</div>
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#collapseTwo">
														<i class="icon-move"></i> Brightness
													</a>
												</div>
												<div id="collapseTwo" class="accordion-body collapse">
													<div class="accordion-inner">
														<p><input type="text" placeholder="Type something…"></p>
														<p>
															<button class="btn" type="button"><i class="icon-white icon-ok"></i></button>
															<button class="btn" type="button"><i class="icon-white icon-remove"></i></button>
														</p>
													</div>
												</div>
											</div>
											<div class="accordion-group">
												<div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#collapseThree">
														<i class="icon-move"></i> Saturation
													</a>
												</div>
												<div id="collapseThree" class="accordion-body collapse">
													<div class="accordion-inner">
														<p><input type="text" placeholder="Type something…"></p>
														<p>
															<button class="btn" type="button"><i class="icon-white icon-ok"></i></button>
															<button class="btn" type="button"><i class="icon-white icon-remove"></i></button>
														</p>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="presets">
										<table class="table">
											<thead>
											<tbody>
											<tr>
												<td>
													<p>MyPreset1</p>
													<ul class="nav nav-tabs nav-stacked">
														<li>Contrast(50)</li>
														<li>Crop(20,20)</li>
														<li>Rotate(90)</li>
													</ul>

													<button class="btn" type="button"><i class="icon-white icon-plus"></i></button>
												</td>
												ac	</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="span8">
								<img src="<?php echo JUri::root() . 'images/' . $this->item->path . '/' . $this->item->filename; ?>">
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php $this->show_options = $params->show_file_options; ?>
		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

		<?php // Do not show the publishing options if the edit form is configured not to. ?>
		<?php if ($params->show_publishing_options == 1) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>


		<?php if ( ! $isModal && $assoc) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS')); ?>
			<?php echo $this->loadTemplate('associations'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php elseif ($isModal && $assoc) : ?>
			<div class="hidden"><?php echo $this->loadTemplate('associations'); ?></div>
		<?php endif; ?>

		<?php if ($this->canDo->get('core.admin')) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_MEDIA_FIELDSET_RULES')); ?>
			<?php echo $this->form->getInput('rules'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
		<input type="hidden" name="forcedLanguage" value="<?php echo $input->get('forcedLanguage', '', 'cmd'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
