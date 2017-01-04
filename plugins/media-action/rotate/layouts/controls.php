<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Media-Action.rotate
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>

<div class="rotate-buttons">
	<p>
		<i class="btn btn-rotate icon-unblock" data-degree="-45"></i>
		<i class="btn btn-rotate icon-undo-2" data-degree="45"></i>
	</p>
	<p>
		<input type="range" min="-360" max="360" name="degree" id="degree" placeholder="" value="0" style="width: 100%"/>
	</p>
</div>

<script type="text/javascript">
	// Just for prototyping @todo Move / vanilla / vue
	(function ($) {
		$('.btn-rotate').click(function(e){
			var degree = $(this).attr('data-degree');
			var $degree = $('#degree');
			var current = $degree.val();

			$degree.val((parseInt(degree) + parseInt(current)) % 360);

			// Just for demo
			$(this).parents('form').first().find('.btn-apply').click();
		});
	}(jQuery));
</script>
