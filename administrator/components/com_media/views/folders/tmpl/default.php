<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var $this MediaViewFolders */

defined('_JEXEC') or die;

$user = JFactory::getUser();
$input = JFactory::getApplication()->input;
$params = JComponentHelper::getParams('com_media');
$lang = JFactory::getLanguage();
$doc = JFactory::getDocument();

// Include jQuery
JHtml::_('jquery.framework');
JHtml::_('script', 'media/folders.js', false, true, false, false, true);
JHtml::_('script', 'media/mediamanager.js', false, true, false, false, true);
JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);

if ($lang->isRtl()) {
    JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);
}

$doc->addScriptDeclaration("var basepath = '" . $params->get('image_path', 'images') . "';");
?>
<style>
    ul#treeData {
        padding-left: 15px;
    }

    ul#treeData li {
        line-height: 170%;
    }

    ul#treeData li a {
        color: #555;
        padding: 3px 2px;
    }

    .thumbnails > li {
        margin-left: 10px;
        margin-right: 10px;
    }
</style>
<script>
    jQuery(document).ready(function($)
    {
        if (window.toggleSidebar)
        {
            toggleSidebar(true);
        }
        else
        {
            $("#j-toggle-sidebar-header").css("display", "none");
            $("#j-toggle-button-wrapper").css("display", "none");
        }
    });
</script>
<div id="system-message-container"></div>
<div class="row-fluid">
    <div id="j-sidebar-container" class="span2">
        <div id="j-toggle-sidebar-wrapper">
            <div id="j-toggle-button-wrapper" class="j-toggle-button-wrapper">
                <div
                    id="j-toggle-sidebar-button"
                    class="j-toggle-sidebar-button hidden-phone hasTooltip"
                    type="button"
                    onclick="toggleSidebar(false); return false;"
                >
                    <span id="j-toggle-sidebar-icon" class="icon-arrow-left-2"></span>
                </div>
            </div>
            <div id="sidebar" class="sidebar">
                <div class="sidebar-nav">
                </div>
            </div>
            <div id="j-toggle-sidebar"></div>
        </div>
        <div class="j-toggle-sidebar-header">
            <h3 style="padding-left: 10px;"><?php echo JText::_('COM_MEDIA_FOLDERS'); ?> </h3>
        </div>
        <div id="tree" class="sidebar">
            <?php echo $this->loadTemplate('folders'); ?>
        </div>
    </div>

    <div id="j-main-container" class="span10">
        <?php if (($user->authorise('core.create', 'com_media')) && $this->require_ftp) : ?>
            <?php echo $this->loadTemplate('ftp'); ?>
        <?php endif; ?>

        <?php if ($user->authorise('core.create', 'com_media')): ?>
            <div id="collapseUpload" class="collapse">
                <?php echo $this->loadTemplate('upload'); ?>
            </div>
            <div id="collapseFolder" class="collapse">
                <?php echo $this->loadTemplate('newfolder'); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?option=com_media" name="adminForm" id="mediamanager-form" method="post"
              enctype="multipart/form-data">
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="cb1" id="cb1" value="0"/>
            <input class="update-folder" type="hidden" name="folder" id="folder"
                   value="<?php echo $this->state->folder; ?>"/>
            <?php echo JHtml::_('form.token'); ?>

            <div id="filesview">
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </form>
    </div>
</div>
