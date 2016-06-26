<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/editor.php';

/**
 * HTML View class for the editor in the Media Manager
 *
 * @since  3.6
 */
class MediaViewEditor extends JViewLegacy
{
    /**
     * @var JApplicationCms
     */
    protected $app;

    /**
     * Execute and display a template script.
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise a Error object.
     *
     * @since   3.6
     */
    public function display($tpl = null)
    {
        $this->app = JFactory::getApplication();

        $filePath = $this->app->input->getPath('file');
        $pluginName = $this->app->input->getCmd('plugin');
        $plugin = $this->getPluginByName($pluginName);

        $this->postUrl = 'index.php?option=com_media&task=editor.post&plugin=' . $pluginName;
        $this->pluginHtml = $plugin->onMediaEditorDisplay($filePath);
        $this->filePath = $filePath;

        parent::display($tpl);
    }

    /**
     * Return the Media Editor plugin by name
     *
     * @param string $pluginName
     *
     * @return JPlugin
     */
    private function getPluginByName($pluginName)
    {
        if (empty($pluginName)) {
            throw new RuntimeException(JText::_('COM_MEDIA_ERROR_UNKNOWN_PLUGIN'));
        }

        $plugin = MediaHelperEditor::loadPlugin($pluginName);

        if ($plugin == false) {
            throw new RuntimeException(JText::_('COM_MEDIA_ERROR_UNKNOWN_PLUGIN'));
        }

        if (method_exists($plugin, 'onMediaEditorDisplay') == false) {
            throw new RuntimeException(JText::_('COM_MEDIA_ERROR_UNKNOWN_PLUGIN'));
        }

        return $plugin;
    }
}
