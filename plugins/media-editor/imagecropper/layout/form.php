<?php
$name       = JFactory::getApplication()->input->getCmd('name');

$filePath   = $displayData['filePath'];
$path_parts = pathinfo($filePath);
$folder     =$path_parts['dirname'];

$session    = JFactory::getSession();
$uploadUrl  = JUri::base() . 'index.php?option=com_media&task=file.upload&tmpl=component&allowovewrite=true&folder='
    . $folder . '&'
    . $session->getName() . '=' . $session->getId()
    . '&' . JSession::getFormToken() . '=1'
    . '&asset=image&format=json';

JHtml::_('jquery.framework');
JHtml::_('script', 'plg_media-editor_imagecropper/cropper.js', false, true, false, false, true);
JHtml::_('script', 'plg_media-editor_imagecropper/cropper-init.min.js', false, true, false, false, true);
JHtml::_('stylesheet', 'plg_media-editor_imagecropper/cropper.css', array(), true);

// TODO get any data-* values and build the options
// eg:   if (typeof image.getAttribute("data-some-attribute") != "undefined") {
//           option1 = image.getAttribute("data-some-attribute");
//       }
// This way no inline script will be injected in the page
?>
<div class="btn-toolbar imagecropper-toolbar">

    <div class="btn-group">
        <button type="button" class="btn" data-method="rotate" data-option="-90" title="<?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_ROTATE_LEFT'); ?> -90&deg;"><?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_ROTATE_LEFT'); ?> -90&deg;</button>
        <button type="button" class="btn" data-method="rotate" data-option="-45">-45&deg;</button>
        <button type="button" class="btn" data-method="rotate" data-option="-30">-30&deg;</button>
        <button type="button" class="btn" data-method="rotate" data-option="-15">-15&deg;</button>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="rotate" data-option="90" title="<?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_ROTATE_RIGHT'); ?> 90&deg;"><?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_ROTATE_RIGHT'); ?> 90&deg;</button>
        <button type="button" class="btn" data-method="rotate" data-option="45">45&deg;</button>
        <button type="button" class="btn" data-method="rotate" data-option="30">30&deg;</button>
        <button type="button" class="btn" data-method="rotate" data-option="15">15&deg;</button>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="scaleX" title="<?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_FLIP_HORIZONTAL'); ?>"><?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_FLIP_HORIZONTAL'); ?></button>
        <button type="button" class="btn" data-method="scaleY" title="<?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_FLIP_VERTICAL'); ?>"><?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_FLIP_VERTICAL'); ?></button>
    </div>

	<div class="btn-group">
		<button type="button" class="btn" data-method="zoom-in" title="<?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_ZOOM_IN'); ?>"><?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_ZOOM_IN'); ?></button>
		<button type="button" class="btn" data-method="zoom-out" title="<?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_ZOOM_OUT'); ?>"><?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_ZOOM_OUT'); ?></button>
	</div>

</div>

<div class="cropper-bg">
    <?php echo JHtml::_('image', COM_MEDIA_BASEURL . '/' . $filePath, '', 'id="joomla-media-image-cropper" data-some-attribute="some value" data-url="'. $uploadUrl . '"'); ?>
</div>
<br />
<input type="checkbox" name="save_copy" id="save_copy" checked="checked"/> <label for="save_copy"><?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_SAVE_COPY') ?></label>
<input type="submit" class="btn" value="<?php echo JText::_('PLG_MEDIA_EDITOR_IMAGECROPPER_FIELD_SUBMIT'); ?>" />
<input type="hidden" name="imagecropper-jsondata" value="" id="imagecropper-jsondata" />