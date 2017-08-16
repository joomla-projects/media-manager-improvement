<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Controller\Controller;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Media\Administrator\Event\OAuthCallbackEvent;

/**
 * Plugin Controller for OAuth2.0 callbacks
 *
 * This controller is used for handle OAuth2 Callbacks
 *
 * @since  __DEPLOY_VERSION__
 */
class Plugin extends Controller
{
	/**
	 * Handles an OAuth Callback request for a specified plugin
	 *
	 * URLs containing [sitename]/administrator/index.php?option=com_media&task=plugin.oauthcallback
	 *  &plugin=[plugin_name]&status=[csrf_token]&....
	 *
	 * Will be handled by this endpoint.
	 * It will select the plugin specified by plugin_name and pass all the data received from the provider
	 *
	 * @return void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function oauthcallback()
	{
		// Get the input
		$input = $this->input->request->getArray();

		// Check if CSRF was set in status or reject
		if (!isset($input['status']))
		{
			jexit("CSRF token was not found");
		}

		// Check if token match with the session
		if (\JSession::getFormToken() != $input['status'])
		{
			jexit("Invalid CSRF token");
		}

		// Load plugin names
		$pluginName = $this->input->request->getString('plugin', null);
		$plugins = PluginHelper::getPlugin('filesystem');

		// If plugin name was not found in parameters redirect back to control panel
		if (!$pluginName || !$this->containsPlugin($plugins, $pluginName))
		{
			$this->displayResponse('Plugin not found!.', 'error');
		}

		// Check if the plugin is disabled, if so redirect to control panel
		if (!PluginHelper::isEnabled('filesystem', $pluginName))
		{
			$this->displayResponse('Plugin ' . $pluginName . ' is disabled.', 'error');
		}

		try{
			// Only import our required plugin, not entire group
			PluginHelper::importPlugin('filesystem', $pluginName);

			// Event parameters
			$eventParameters = ['context' => $pluginName, 'parameters' => $input];
			$event = new OAuthCallbackEvent('onFilesystemOAuthCallback', $eventParameters);

			// Get results from event
			$eventResults = (array)\JFactory::getApplication()->triggerEvent('onFilesystemOAuthCallback', $event);

			// If event was not triggered in the selected Plugin, raise a warning and fallback to Control Panel
			if (!$eventResults)
			{
				$this->displayResponse('Plugin ' . $pluginName . ' should have implemented '
					. 'onFilesystemOAuthCallback method');
			}

			// Check if any action is specified
			if (!isset($eventResults['action']))
			{
				$this->displayResponse('Action must be set in '. $pluginName, 'error');
			}

			$action = $eventResults['action'];
			$message = null;

			// If there are any messages display them
			if (isset($eventResults['message']))
			{
				$message = $eventResults['message'];
				$messageType = (isset($eventResults['message_type']) ? $eventResults['message_type'] : '');

				\JFactory::getApplication()->enqueueMessage($message, $messageType);
			}

			/**
			 * Execute actions defined by the plugin
			 * Supported actions
			 *  - close         : Closes the current window, use this only for windows opened by javascript
			 *  - redirect      : Redirect to a URI defined in 'redirect_uri' parameter, if not fallback to control panel
			 *  - media-manager : Redirect to Media Manager
			 *  - control-panel : Redirect to Control Panel
			 */
			switch ($action)
			{
				/**
				 * Close a window opened by developer
				 * Use this for close New Windows opened for OAuth Process
				 */
				case 'close':
					$this->setRedirect(\JRoute::_('index.php?option=com_media&view=plugin&action=close', false));
					break;

				// Redirect browser to any page specified by the user
				case 'redirect':
					if (!isset($eventResults['redirect_uri']))
					{
						$this->displayResponse('Redirect Uri must be set in '. $pluginName, 'error');
					}
					$this->setRedirect(($eventResults['redirect_uri']));
					break;

				// Redirect browser to Media Manager
				case 'media-manager':
					$this->setRedirect(\JRoute::_('index.php?option=com_media', false));
					break;

				// Redirect browser to Control Panel
				case 'control-panel':
					$this->setRedirect(\JRoute::_('index.php', false));
					break;

				// Redirect browser to control panel with a warning when no action specified
				default:
					$this->displayResponse('Unknown action '. $action .' was defined in ' . $pluginName, 'warning');
					break;
			}

			$this->redirect();
		}
		catch (\Exception $e)
		{
			// Display any error
			$this->displayResponse($e->getMessage(), 'error');
		}
	}

	/**
	 * @param   string   $message  Message to be displayed
	 * @param   string   $type     Message type [error, warning, notice or empty it]
	 *
	 * @return void
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private function displayResponse($message, $type = 'error')
	{
		\JFactory::getApplication()->enqueueMessage($message, $type);
		$this->setRedirect(\JRoute::_('index.php', false));
		$this->redirect();
	}

	/**
	 * Check whether a plugin exists in given plugin array
	 *
	 * @param   array   $plugins     Array of plugin names
	 * @param   string  $pluginName  Plugin name to look up
	 *
	 * @return bool
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	private function containsPlugin($plugins, $pluginName)
	{
		foreach ($plugins as $plugin)
		{
			if ($plugin->name == $pluginName)
			{
				return true;
			}
		}

		return false;
	}
}