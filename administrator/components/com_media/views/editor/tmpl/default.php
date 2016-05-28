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
<form class="form-horizontal form-validate" name="mediaEditorForm" id="mediaEditorForm" method="post" action="<?php echo $this->postUrl; ?>">
	<?php echo $this->pluginHtml; ?>
	<input type="hidden" name="file" value="<?php echo $this->filePath; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
