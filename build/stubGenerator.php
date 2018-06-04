<?php
/**
 * @package    Joomla.Build
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Set flag that this is a parent file.
const _JEXEC = 1;

// Import namespaced classes
use Joomla\CMS\Application\CliApplication;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the Platform with legacy libraries.
require_once JPATH_LIBRARIES . '/bootstrap.php';

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Utility CLI to generate a stubs file holding mapped classes.
 *
 * As Joomla transitions its core classes from residing in the global PHP namespace to using namespaced PHP classes, it will be a common
 * occurrence for developers to work in an environment where their code is still using the old class names which may not exist in newer
 * Joomla releases except for in PHP's autoloader as a class alias.  This script therefore allows developers to generate a mapping
 * file they can use in their local environment which will create "real" classes for the aliased class names and allow things like
 * IDE auto completion to work normally.
 *
 * When this script is run, a `stubs.php` file will be generated at the root of your Joomla installation holding all of the mapping
 * information.  Note that this file will raise some IDE errors as it will generate stub classes extending a final class (something
 * not allowed in PHP).  Therefore it is suggested that inspections on this file are disabled.
 *
 * @since  3.0
 */
class StubGenerator extends CliApplication
{
	/**
	 * Entry point for CLI script
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function doExecute()
	{
		$file = "<?php\n";

		// Loop the aliases to generate the stubs data
		foreach (JLoader::getDeprecatedAliases() as $alias)
		{
			$oldName           = $alias['old'];
			$newName           = $alias['new'];
			$deprecatedVersion = $alias['version'];

			// Figure out if the alias is for a class or interface
			$reflection = new ReflectionClass($newName);
			$type       = $reflection->isInterface() ? 'interface' : 'class';
			$modifier   = (!$reflection->isInterface() && $reflection->isFinal()) ? 'final ' : '';
			$modifier   = ($reflection->isAbstract() && !$reflection->isInterface()) ? $modifier . 'abstract ' : $modifier;

			// If a deprecated version is available, write a stub class doc block with a deprecated tag
			if ($deprecatedVersion !== false)
			{
				$file .= <<<PHP
/**
 * @deprecated $deprecatedVersion Use $newName instead.
 */

PHP;

			}

			$file .= "$modifier$type $oldName extends $newName {}\n\n";
		}

		// And save the file locally
		file_put_contents(JPATH_ROOT . '/stubs.php', $file);

		$this->out('Stubs file written', true);
	}
}

// Instantiate the application and execute it
CliApplication::getInstance('StubGenerator')->execute();
