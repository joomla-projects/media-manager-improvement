<?php
// Get action
$action = JFactory::getApplication()->input->getWord('action');

/**
 * Close current window
 *
 * This method only works for windows that were opened using window.open(url);
 * It will close such windows on this command.
 * Windows opened by user are not affected.
 */
if ($action == 'close')
{
    \JFactory::getDocument()->addScriptDeclaration('<script>window.close();</script>');
}

