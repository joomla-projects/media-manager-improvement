<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$input         = JFactory::getApplication()->input;
$actionUrl     = 'index.php?option=com_media&amp;task=folder.create&amp;tmpl=' . $input->getCmd('tmpl', 'index');
$currentFolder = $this->state->folder;
?>
<form
	action="<?php echo $actionUrl ?>"
	name="folderForm" id="folderForm" class="form-inline" method="post">
	<div class="path">
		<input type="text" id="folderpath" readonly="readonly" class="update-folder" />
		<input type="text" id="foldername" name="foldername" />
		<input class="update-folder" type="hidden" name="folderbase" id="folderbase"
		       value="<?php echo $currentFolder; ?>" />
		<button type="submit" class="btn">
			<span class="icon-folder-open"></span>
			<?php echo JText::_('COM_MEDIA_CREATE_FOLDER'); ?>
		</button>
	</div>
	<?php echo JHtml::_('form.token'); ?>
</form>
