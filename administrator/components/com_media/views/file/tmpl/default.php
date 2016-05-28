<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include jQuery
JHtml::_('jquery.framework');

$fileProperties = $this->fileProperties;
$layoutFile     = 'file.' . $fileProperties['file_type'];
$layout         = new JLayoutFile($layoutFile);
$formUrl        = 'index.php?option=com_media';
?>
<form action="<?php echo $formUrl ?>" method="post" id="adminForm" name="adminForm">
	<h1><?php echo $fileProperties['name']; ?></h1>

	<div>
		<?php echo $layout->render($fileProperties); ?>
	</div>

	<div>
		<table class="table table-striped">
			<tr>
				<td><?php echo JText::_('COM_MEDIA_NAME'); ?></td>
				<td><?php echo $fileProperties['path_relative']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_MEDIA_EXTENSION'); ?></td>
				<td><?php echo $fileProperties['extension']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_MEDIA_FILESIZE'); ?></td>
				<td><?php echo $fileProperties['size']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_MEDIA_MIMETYPE'); ?></td>
				<td><?php echo $fileProperties['mime_type']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_MEDIA_FILE_TYPE'); ?></td>
				<td><?php echo $fileProperties['file_type']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_MEDIA_FILE_ADAPTER'); ?></td>
				<td><?php echo $fileProperties['file_adapter']; ?></td>
			</tr>
		</table>
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="file" value="<?php echo $fileProperties['path_relative']; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>