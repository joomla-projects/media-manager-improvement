<?php
$inp = JFactory::getApplication()->input;
$name = $inp->getCmd('name');
$filePath = $displayData['filePath'];
?>
<div class="btn-toolbar imagecropper-toolbar">

    <div class="btn-group">
        <button type="button" class="btn" data-method="rotate" data-option="-90" title="Rotate -90°"><?php JText::printf('PLG_MEDIA-EDITOR_IMAGECROPPER_ROTATE_LEFT') ?> -90°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-45">-45°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-30">-30°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-15">-15°</button>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="rotate" data-option="90" title="Rotate 90°"><?php JText::printf('PLG_MEDIA-EDITOR_IMAGECROPPER_ROTATE_RIGHT') ?> 90°</button>
        <button type="button" class="btn" data-method="rotate" data-option="45">45°</button>
        <button type="button" class="btn" data-method="rotate" data-option="30">30°</button>
        <button type="button" class="btn" data-method="rotate" data-option="15">15°</button>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="scaleX" data-option="-1" title="Flip Horizontal"><?php JText::printf('PLG_MEDIA-EDITOR_IMAGECROPPER_FLIP_HORIZONTAL') ?></button>
        <button type="button" class="btn" data-method="scaleY" data-option="-1" title="Flip Vertical"><?php JText::printf('PLG_MEDIA-EDITOR_IMAGECROPPER_FLIP_VERTICAL') ?></button>
    </div>

</div>

<div class="cropper-bg">
    <?php echo JHtml::_('image', COM_MEDIA_BASEURL . '/' . $filePath, '', 'id="media-image"'); ?>
</div>
<br />
<input type="checkbox" name="save_copy" id="save_copy" checked="checked"/> <label for="save_copy"><?php JText::printf('PLG_MEDIA-EDITOR_IMAGECROPPER_SAVE_COPY') ?></label>
<input type="submit" name="<?php echo JText::_('PLG_MEDIA-EDITOR_IMAGECROPPER_FIELD_SUBMIT'); ?>" />
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
            cropper[$(this).data('method')]($(this).data('option'));
        });
    });
</script>