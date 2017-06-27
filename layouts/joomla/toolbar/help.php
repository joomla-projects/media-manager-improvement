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

$id     = isset($displayData['id']) ? $displayData['id'] : '';
$doTask = $displayData['doTask'];
$text   = $displayData['text'];
?>
<button<?php echo $id; ?> onclick="<?php echo $doTask; ?>" rel="help" class="btn btn-outline-info btn-sm">
	<span class="icon-question-sign" aria-hidden="true"></span>
	<?php echo $text; ?>
</button>
