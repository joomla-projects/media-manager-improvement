<?php
$inp = JFactory::getApplication()->input;
$name = $inp->getCmd('name');
$filePath = $displayData['filePath'];
?>
<div class="btn-toolbar imagecropper-toolbar">

    <div class="btn-group">
        <button type="button" class="btn" data-method="rotate" data-option="-90" title="<?php echo JText::_('PLG_IMAGECROPPER_ROTATE_LEFT'); ?> -90°"><?php echo JText::_('PLG_IMAGECROPPER_ROTATE_LEFT'); ?> -90°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-45">-45°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-30">-30°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-15">-15°</button>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="rotate" data-option="90" title="Rotate 90°"><?php echo JText::_('PLG_IMAGECROPPER_ROTATE_RIGHT'); ?> 90°</button>
        <button type="button" class="btn" data-method="rotate" data-option="45">45°</button>
        <button type="button" class="btn" data-method="rotate" data-option="30">30°</button>
        <button type="button" class="btn" data-method="rotate" data-option="15">15°</button>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="scaleX" title="<?php echo JText::_('PLG_IMAGECROPPER_FLIP_HORIZONTAL'); ?>"><?php echo JText::_('PLG_IMAGECROPPER_FLIP_HORIZONTAL'); ?></button>
        <button type="button" class="btn" data-method="scaleY" title="<?php echo JText::_('PLG_IMAGECROPPER_FLIP_VERTICAL'); ?>"><?php echo JText::_('PLG_IMAGECROPPER_FLIP_VERTICAL'); ?></button>
    </div>

	<div class="btn-group">
		<button type="button" class="btn" data-method="zoom-in" title="<?php echo JText::_('PLG_IMAGECROPPER_ZOOM_IN'); ?>"><?php echo JText::_('PLG_IMAGECROPPER_ZOOM_IN'); ?></button>
		<button type="button" class="btn" data-method="zoom-out" title="<?php echo JText::_('PLG_IMAGECROPPER_ZOOM_OUT'); ?>"><?php echo JText::_('PLG_IMAGECROPPER_ZOOM_OUT'); ?></button>
	</div>

</div>

<div class="cropper-bg">
    <?php echo JHtml::_('image', COM_MEDIA_BASEURL . '/' . $filePath, '', 'id="media-image"'); ?>
</div>
<br />
<input type="checkbox" name="save_copy" id="save_copy" checked="checked"/> <label for="save_copy"><?php echo JText::_('PLG_IMAGECROPPER_SAVE_COPY') ?></label>
<input type="submit" class="btn" value="<?php echo JText::_('PLG_IMAGECROPPER_FIELD_SUBMIT'); ?>" />
<input type="hidden" name="imagecropper-jsondata" value="" id="imagecropper-jsondata" />

<script type="text/javascript">
    jQuery(function ($) {
        var image = document.getElementById('media-image');
        var cropper = new Cropper(image, {
            aspectRatio: 16 / 9,
            crop: function (e) {
                var oDetails = e.detail;
                var json = [
                    '{"x":' + oDetails.x,
                    '"y":' + oDetails.y,
                    '"height":' + oDetails.height,
                    '"width":' + oDetails.width,
                    '"rotate":' + oDetails.rotate,
                    '"scaleX":' + oDetails.scaleX,
                    '"scaleY":' + oDetails.scaleY + '}'
                ].join();

                $('#imagecropper-jsondata').val(json);
            }
        });

        $('.btn-toolbar button').click(function() {
	        var action = $(this).data('method');

	        switch (action) {
		        case 'zoom-in':
			        cropper.zoom(0.1);
			        break;

		        case 'zoom-out':
			        cropper.zoom(-0.1);
			        break;

		        case 'rotate':
			        cropper.rotate($(this).data('option'));
			        break;

		        case 'scaleX':
			        cropper.scaleX(-cropper.getData().scaleX || -1);
			        break;

		        case 'scaleY':
			        cropper.scaleY(-cropper.getData().scaleY || -1);
			        break;

		        // No default
	        }
        });
    });
</script>