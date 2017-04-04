<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Add javascripts

JHtml::_('behavior.core');
JHtml::_('behavior.formvalidator');
JHtml::_('script', 'vendor/eventbusjs/eventbus.min.js', array('version' => 'auto', 'relative' => true));

JHtml::_('bootstrap.framework');
JHtml::_('script', 'com_media/edit.js', array('version' => 'auto', 'relative' => true));

$params = JComponentHelper::getParams('com_media');

// Populate the media config
$config = array(
	'apiBaseUrl'              => JUri::root() . 'administrator/index.php?option=com_media&format=json',
	'csrfToken'               => JSession::getFormToken(),
	'filePath'                => $params->get('file_path', 'images'),
	'fileBaseUrl'             => JUri::root() . $params->get('file_path', 'images'),
	'uploadPath'              => $this->file,
	'editViewUrl'             => JUri::root() . 'administrator/index.php?option=com_media&view=file',
	'allowedUploadExtensions' => $params->get('upload_extensions', ''),
	'maxUploadSizeMb'         => $params->get('upload_maxsize', 10),
);
JFactory::getDocument()->addScriptOptions('com_media', $config);

/**
 * @var JForm $form
 */
$form = $this->form;
?>
<style>
	.btn-group {
		display: block;
	}
</style>
<form action="#" method="post" name="adminForm" id="media-form" class="form-validate">
<?php
$fieldSets = $form->getFieldsets();

if ($fieldSets)
{
	echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'attrib-' . reset($fieldSets)->name));

	echo JLayoutHelper::render('joomla.edit.params', $this);

	echo JHtml::_('bootstrap.endTabSet');
}
// @TODO Logic for handling other types of media, not only images!!!!!
?>

</form>
<p>Edit area: </p>
<span class="image-container">
    <img id="media-edit-file" src="<?php echo $this->fullFilePath ?>" width="100%"/>
</span>
<p>Preview: </p>
<span class="image-container-preview">
    <img id="media-edit-file-new" src="<?php echo $this->fullFilePath ?>" style="max-width:100%"/>
</span>