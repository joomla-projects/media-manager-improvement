<?php

namespace Joomla\MediaManager\Plugin\Action;

defined('_JEXEC') or die;

// @todo Autoloader
require_once __DIR__ . '/PluginInterface.php';

/**
 * Plugin Interface for the Media Type Plugins (E.g. Image, PDF)
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class Plugin extends \JPlugin  implements PluginInterface
{
}
