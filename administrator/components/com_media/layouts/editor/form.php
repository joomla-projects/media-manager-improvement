<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$plugin = $displayData['plugin'];
$filePath = $displayData['filePath'];
$postUrl = $displayData['postUrl'];
?>
<form method="post" action="<?php echo $postUrl ?>">
	<?php echo $plugin; ?>
	<input type="hidden" name="file" value="<?php echo $filePath ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
