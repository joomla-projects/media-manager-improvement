<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  MediaEditor.Copy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Class PlgMediaEditorCopy
 */
class PlgMediaEditorCopy extends JPlugin
{
	/**
	 * Load the application automatically
	 */
	protected $app;

	/**
	 * Load the language file automatically
	 *
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * Method to check whether this media editor plugin is allowed on a specific fileType
	 *
	 * @param $fileType string
	 *
	 * @return bool
	 */
	public function onMediaEditorAllowed($fileType)
	{
		return true;
	}

	/**
	 * Method to return the button label of this plugin
	 *
	 * @return string
	 */
	public function onMediaEditorButtonLabel()
	{
        $doc = JFactory::getDocument();
        $doc->addStyleDeclaration('.icon-copy:before { content: "\2a"; }');

		return JText::_('PLG_MEDIA-EDITOR_COPY_BUTTON_LABEL');
	}

	/**
	 * Method to return the HTML shown in a modal popup within the Media Manager
	 *
	 * @param $filePath string
	 *
	 * @return string
	 */
	public function onMediaEditorDisplay($filePath)
	{
		$data   = array('filePath' => $filePath, 'toFile' => $this->getDefaultDestination($filePath));
		$layout = new JLayoutFile('form', __DIR__ . '/layout');

		return $layout->render($data);
	}

	/**
	 * Method to process the given file
	 *
	 * @param $filePath string
	 *
	 * @return false|string
	 */
	public function onMediaEditorProcess($filePath)
	{
		// Calculate the right variables
		$newFile = $this->app->input->getFile('toFile');

		$folder      = dirname($filePath);
		$newFilePath = $folder . '/' . $newFile;

		if ($newFilePath == $filePath)
		{
			return false;
		}
        
        if (file_exists($newFilePath))
        {
            throw new InvalidArgumentException(JText::_('COM_MEDIA_ERROR_FILE_EXISTS'));
        }

		// Rename the file
        // @todo: Put this properly in a file wrapper
		copy($filePath, $newFilePath);

        $returnPath = str_replace(COM_MEDIA_BASE, '', $newFilePath);

		// Return the new URL
		return JRoute::_('index.php?option=com_media&view=file&view=file&file=' . $returnPath, false);
	}

    private function getDefaultDestination($path)
    {
        $file = basename($path);
        $folder = dirname($path);

        $i = 1;

        while (true)
        {
            $newFile = preg_replace('/\.([a-zA-Z0-9]+)$/', '_'.$i.'.\1', $file);

            // @todo: Put this properly in a file wrapper
            if (!file_exists(COM_MEDIA_BASE . '/' . $folder . '/' . $newFile))
            {
                return $newFile;
            }

            $i++;
        }
    }
}
