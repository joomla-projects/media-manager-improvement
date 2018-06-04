<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_banners&view=tracks'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="col-md-10">
			<div id="j-main-container" class="j-main-container">
				<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
				<?php if (empty($this->items)) : ?>
					<joomla-alert type="warning"><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></joomla-alert>
				<?php else : ?>
					<table class="table table-striped">
						<thead>
							<tr>
								<th class="title">
									<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_NAME', 'b.name', $listDirn, $listOrder); ?>
								</th>
								<th style="width:20%" class="nowrap">
									<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_CLIENT', 'cl.name', $listDirn, $listOrder); ?>
								</th>
								<th style="width:10%" class="nowrap d-none d-md-table-cell">
									<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_TYPE', 'a.track_type', $listDirn, $listOrder); ?>
								</th>
								<th style="width:10%" class="nowrap d-none d-md-table-cell">
									<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_COUNT', 'a.count', $listDirn, $listOrder); ?>
								</th>
								<th style="width:10%" class="nowrap d-none d-md-table-cell">
									<?php echo JHtml::_('searchtools.sort', 'JDATE', 'a.track_date', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5">
									<?php echo $this->pagination->getListFooter(); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php foreach ($this->items as $i => $item) : ?>
								<tr class="row<?php echo $i % 2; ?>">
									<td>
										<?php echo $item->banner_name; ?>
										<div class="small">
											<?php echo JText::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
										</div>
									</td>
									<td>
										<?php echo $item->client_name; ?>
									</td>
									<td class="small d-none d-md-table-cell">
										<?php echo $item->track_type == 1 ? JText::_('COM_BANNERS_IMPRESSION') : JText::_('COM_BANNERS_CLICK'); ?>
									</td>
									<td class="d-none d-md-table-cell">
										<?php echo $item->count; ?>
									</td>
									<td class="d-none d-md-table-cell">
										<?php echo JHtml::_('date', $item->track_date, JText::_('DATE_FORMAT_LC4') . ' H:i'); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
				<?php // Load the export form ?>
				<?php echo JHtml::_(
					'bootstrap.renderModal',
					'downloadModal',
					array(
						'title'       => JText::_('COM_BANNERS_TRACKS_DOWNLOAD'),
						'url'         => JRoute::_('index.php?option=com_banners&amp;view=download&amp;tmpl=component'),
						'height'      => '370px',
						'width'       => '300px',
						'modalWidth'  => '40',
						'footer'      => '<a class="btn" data-dismiss="modal" type="button"'
								. ' onclick="jQuery(\'#downloadModal iframe\').contents().find(\'#closeBtn\').click();">'
								. JText::_('COM_BANNERS_CANCEL') . '</a>'
								. '<button class="btn btn-success" type="button"'
								. ' onclick="jQuery(\'#downloadModal iframe\').contents().find(\'#exportBtn\').click();">'
								. JText::_('COM_BANNERS_TRACKS_EXPORT') . '</button>',
					)
				); ?>
				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
