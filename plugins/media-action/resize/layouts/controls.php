<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.resize
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Get the image properties
list($imageWidth, $imageHeight, $imageType, $imageAttr) = getimagesize(JPATH_ROOT . '/' . $displayData['filePath']);
?>

<div class="RESIZE-buttons">
	<p>
		<label for="width"><?php echo JText::_('PLG_MEDIA-ACTION_RESIZE_WIDTH'); ?></label>
		<input type="number" min="0" name="width" id="width" value="<?php echo $imageWidth; ?>" />
	</p>
	<p>
		<label for="height"><?php echo JText::_('PLG_MEDIA-ACTION_RESIZE_HEIGHT'); ?></label>
		<input type="number" min="0" name="height" id="height" value="<?php echo $imageHeight; ?>" />
	</p>
	<p>
		<input type="checkbox" name="keep_proportions" id="keep_proportions" checked="checked" />
		<label for="keep_proportions"><?php echo JText::_('PLG_MEDIA-ACTION_RESIZE_KEEP_PROPORTIONS'); ?></label>
	</p>
</div>


<script type="text/javascript">
	// Prototyping only
	(function ($) {
		$(document).ready(function(){
			var $width = $('#width');
			var $height = $('#height');
			var $keep = $('#keep_proportions');
			var $file = $('#file');

			var imageWidth = <?php echo $imageWidth; ?>;
			var imageHeight = <?php echo $imageHeight; ?>;

			$width.keyup(function(e) {
				var keep = $keep.is(":checked");
				var height = $height.val();
				var width = $(this).val();

				if (keep) {
					var ratio = width / imageWidth;

					height = imageHeight * ratio;

					$height.val(height);
				}

				console.log(width);
				$file.css('height', height  + 'px');
				$file.css('width', width + 'px');
			});
		});
	}(jQuery));
</script>
