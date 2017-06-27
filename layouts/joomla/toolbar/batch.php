<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JHtml::_('behavior.core');

$id    = isset($displayData['id']) ? $displayData['id'] : '';
$title = $displayData['title'];
JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
JText::script('ERROR');
$message = "{'error': [Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')]}";
$alert = "Joomla.renderMessages(" . $message . ")";
?>
<button<?php echo $id; ?> data-toggle="modal" onclick="if (document.adminForm.boxchecked.value==0){<?php echo $alert; ?>}else{jQuery( '#collapseModal' ).modal('show'); return true;}" class="btn btn-outline-primary btn-sm">
	<span class="icon-checkbox-partial" aria-hidden="true" title="<?php echo $title; ?>"></span>
	<?php echo $title; ?>
</button>
