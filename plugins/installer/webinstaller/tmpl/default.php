<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Installer.Webinstaller
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var PlgInstallerWebinstaller $this */

$dir = $this->isRTL() ? ' dir="ltr"' : '';

?>

<div id="jed-container" class="tab-pane">
	<div class="card" id="web-loader">
		<div class="card-body">
			<h2 class="card-title"><?php echo Text::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING'); ?></h2>
		</div>
	</div>
	<div class="alert alert-error hidden" id="web-loader-error">
		<a class="close" data-dismiss="alert">×</a><?php echo Text::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING_ERROR'); ?>
	</div>
</div>

<fieldset class="form-group hidden" id="uploadform-web"<?php echo $dir; ?>>
	<p><strong><?php echo Text::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM'); ?></strong></p>
	<dl>
		<dt id="uploadform-web-name-label"><?php echo Text::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_NAME'); ?></dt>
		<dd id="uploadform-web-name"></dd>
		<dt><?php echo Text::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_URL'); ?></dt>
		<dd id="uploadform-web-url"></dd>
	</dl>
	<div class="card card-light">
		<div class="card-body">
			<div class="card-text">
				<input type="button" class="btn btn-primary" value="<?php echo Text::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton<?php echo $this->getInstallFrom() != '' ? 4 : 5; ?>()" />
				<input type="button" class="btn btn-secondary" value="<?php echo Text::_('JCANCEL'); ?>" onclick="Joomla.installfromwebcancel()" />
			</div>
		</div>
	</div>
</fieldset>
