<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_redirect
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
$params    = $this->params;
$separator = $params->get('separator', '|');
?>

<div class="container">
	<div class="row">
		<div class="control-group col-md-12">
            <p><?php echo JText::sprintf('COM_REDIRECT_BATCH_TIP', $separator); ?></p>
			<div class="controls">
				<textarea class="col-md-12" rows="10" aria-required="true" value="" id="batch_urls" name="batch_urls"></textarea>
			</div>
		</div>
	</div>
</div>
