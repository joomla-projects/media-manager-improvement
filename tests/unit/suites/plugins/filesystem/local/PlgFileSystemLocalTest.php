<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


class PlgFileSystemLocalTest extends TestCaseDatabase
{
	private $pluginClass = null;

	private $root = null;

	protected function setUp()
	{
		// Set up the application and session
		JFactory::$application = $this->getMockCmsApp();
		JFactory::$session     = $this->getMockSession();

		// Register the needed classes
		JLoader::register('JPath', JPATH_PLATFORM . '/joomla/filesystem/path.php');
		JLoader::register('JFolder', JPATH_PLATFORM . '/joomla/filesystem/folder.php');

		// Import plugin
		JLoader::import('filesystem.local.local', JPATH_PLUGINS);

		$dispatcher = $this->getMockDispatcher();
		$plugin = array(
			'name' => 'local',
			'type' => 'filesystem',
			'params' => new \JRegistry,
		);

		$this->pluginClass = new PlgFileSystemLocal($dispatcher, $plugin);

		// Set up the temp root folder
		$this->root = JPath::clean(JPATH_TESTS . '/tmp/test/');
		JFolder::create($this->root);
	}

	protected function tearDown()
	{
		JFolder::delete($this->root);
	}

	public function testOnFileSystemGetAdapters()
	{
		$adapter = $this->pluginClass->onFileSystemGetAdapters($this->root);
		self::assertInstanceOf('MediaFileAdapterLocal', $adapter);
	}
}
