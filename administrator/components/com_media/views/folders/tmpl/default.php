<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var $this MediaViewFolders */

defined('_JEXEC') or die;

$user   = JFactory::getUser();
$input  = JFactory::getApplication()->input;
$params = JComponentHelper::getParams('com_media');
$lang   = JFactory::getLanguage();
$doc    = JFactory::getDocument();

// Include jQuery
JHtml::_('jquery.framework');
JHtml::_('script', 'media/folders.js', false, true, false, false, true);
JHtml::_('script', 'media/mediamanager.js', false, true, false, false, true);
JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);

if ($lang->isRtl())
{
	JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);
}

$doc->addScriptDeclaration("var basepath = '" . $params->get('image_path', 'images') . "';");
?>
<div class="row-fluid">
	<!-- Begin Content -->
	<div class="span2 sidebar" style="border-right: 1px solid lightgrey; height:100%;">
		<h3 style="padding-left: 10px;"><?php echo JText::_('COM_MEDIA_FOLDERS'); ?> </h3>
		<div id="tree">
			<?php echo $this->loadTemplate('folders'); ?>
		</div>
	</div>
	<div class="span10">
		<?php if (($user->authorise('core.create', 'com_media')) && $this->require_ftp) : ?>
			<?php echo $this->loadTemplate('ftp'); ?>
		<?php endif; ?>

		<?php if ($user->authorise('core.create', 'com_media')): ?>
			<div id="collapseUpload" class="collapse">
				<?php echo $this->loadTemplate('upload'); ?>
			</div>
			<div id="collapseFolder" class="collapse">
				<?php echo $this->loadTemplate('newfolder'); ?>
			</div>
		<?php endif; ?>

		<form action="index.php?option=com_media" name="adminForm" id="mediamanager-form" method="post"
		      enctype="multipart/form-data">
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="cb1" id="cb1" value="0"/>
			<input class="update-folder" type="hidden" name="folder" id="folder"
			       value="<?php echo $this->state->folder; ?>"/>
			<?php echo JHtml::_('form.token'); ?>

			<div id="filesview">
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
