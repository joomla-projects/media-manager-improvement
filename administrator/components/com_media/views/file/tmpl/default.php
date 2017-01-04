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
JHtml::_('formbehavior.chosen', 'select');

JHtml::_('stylesheet', 'media/media-file.css', array('version' => 'auto', 'relative' => true));
?>
<div class="form-inline form-inline-header">

	<div class="row-fluid">
		<div class="span9">
			<fieldset class="adminform">
				<div class="container">
					<div class="row-fluid">
						<div class="span1">
							<ul id="myTab" class="nav nav-pills">
								<?php foreach ($this->pluginCategories as $i => $category) : ?>
									<?php $alias = JApplicationHelper::stringURLSafe($category); ?>
									<li<?php echo ($i == 0) ? ' class="active"' : ''; ?>>
										<a href="#tab<?php echo $alias; ?>"
										   data-toggle="tab" class="hasTooltip" data-placement="right"
										   title="<?php echo $category; ?>">
											<span class="icon-<?php echo $alias; ?>">l</span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>

						<div class="span3">
							<div class="tab-content">
								<?php foreach ($this->pluginCategories as $i => $category) : ?>
									<?php $alias = JApplicationHelper::stringURLSafe($category); ?>
									<div class="tab-pane <?php echo ($i == 0) ? ' active' : ''; ?>"
									     id="tab<?php echo $alias; ?>">
										<div class="accordion" id="accordion<?php echo $alias; ?>">
											<?php foreach ($this->plugins as $plugin) : ?>
												<?php
												// @todo improve
												if ($plugin->getCategory() != $category)
												{
													continue;
												}
												?>
												<div class="accordion-group">
													<div class="accordion-heading">
														<a class="accordion-toggle" data-toggle="collapse"
														   data-parent="#accordion2" href="#collapseOne">
															<i class="icon-<?php echo $plugin->getIconClass(); ?>"></i> <?php echo JText::_($plugin->getTitle()); ?>
														</a>
													</div>
													<div id="collapseOne" class="accordion-body collapse in">
														<form>
															<div class="accordion-inner">
																<div class="plugin-content">
																	<?php
																	echo $plugin->getControls($this->file);
																	?>

																	<input type="hidden" name="plugin"
																	       value="<?php echo $plugin->getName(); ?>">
																</div>
																<div class="plugin-controls">
																	<button class="btn btn-apply" type="button"><i
																				class="icon-white icon-ok"></i></button>
																	<button class="btn btn-cancel" type="button"><i
																				class="icon-white icon-remove"></i>
																	</button>
																</div>
															</div>
														</form>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								<?php endforeach; ?>

							</div><!-- //Tab content -->
						</div>
						<div class="span8">
							<?php // @todo fileextension ?>
							<img src="<?php echo JUri::root() . $this->file ?>" id="file"/>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</div>

</div>

<script>
    // Just for prototype
    (function ($) {
        var url = 'index.php?option=com_media&format=json&task=action.preview&file=<?php echo $this->file ?>';

        $('.btn-apply').click(function () {
            var formData = $(this).parents('form').first().serialize();

            console.log(formData);

            $.ajax(url, {
                method: 'post',
                data: formData
            }).success(function (data) {
                $('#file').attr('src', 'data:image/png;base64,' + data);
            });
        })
    }(jQuery));
</script>
