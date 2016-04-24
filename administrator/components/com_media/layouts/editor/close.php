<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<script type="text/javascript">
	<?php if (!empty($displayData['redirectUrl'])) : ?>
	window.parent.location = '<?php echo $displayData['redirectUrl']; ?>';
	<?php else : ?>
	window.parent.location.reload();
	<?php endif; ?>
</script>
