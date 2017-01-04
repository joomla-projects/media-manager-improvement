<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.resize
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// @todo load from image
$dimensions = array('width' => 294, 'height' => 44);
?>

<div class="RESIZE-buttons">
	<p>
		<label for="width"><?php echo JText::_('PLG_MEDIA-ACTION_RESIZE_WIDTH'); ?></label>
		<input type="number" min="0" name="width" id="width" value="<?php echo $dimensions['width']; ?>" />
	</p>
	<p>
		<label for="height"><?php echo JText::_('PLG_MEDIA-ACTION_RESIZE_HEIGHT'); ?></label>
		<input type="number" min="0" name="height" id="height" value="<?php echo $dimensions['height']; ?>" />
	</p>
	<p>
		<input type="checkbox" name="keep_proportions" id="keep_proportions" checked="checked" />
		<?php echo JText::_('PLG_MEDIA-ACTION_RESIZE_KEEP_PROPORTIONS'); ?>
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

			var imageWidth = <?php echo $dimensions['width']; ?>;
			var imageHeight = <?php echo $dimensions['height']; ?>;

			$width.keyup(function(e) {
				var keep = $keep.is(":checked");

				if (keep) {
					var width = $(this).val();

					var ratio = width / imageWidth;

					var height = imageHeight * ratio;

					$height.val(height);

					$file.css('max-width', width);
					$file.css('max-height', height);
				}
			});
		});

	}(jQuery));
</script>
