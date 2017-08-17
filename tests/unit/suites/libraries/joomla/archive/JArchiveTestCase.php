<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Archive
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

/**
 * Abstract test case for archive package tests
 *
 * @package     Joomla.UnitTest
 * @subpackage  Archive
 * @since       3.1
 */
abstract class JArchiveTestCase extends TestCase
{
	/**
	 * Output path
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $outputPath;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->outputPath = __DIR__ . '/output/' . uniqid();

		if (!is_dir($this->outputPath))
		{
			mkdir($this->outputPath, 0777, true);
		}

		if (!is_dir($this->outputPath))
		{
			$this->markTestSkipped('We can not create the output dir, so skip all tests');
		}

		$this->saveFactoryState();

		// We need a configuration with a tmp_path set
		JFactory::$config = new Registry(['tmp_path' => __DIR__ . '/output']);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function tearDown()
	{
		if (is_dir($this->outputPath))
		{
			// delete files in output directory
			foreach (glob("{$this->outputPath}/*") as $file)
			{
				unlink($file);
			}
			rmdir($this->outputPath);
		}

		$this->restoreFactoryState();

		unset($this->outputPath);
		parent::tearDown();
	}
}
