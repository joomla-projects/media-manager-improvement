<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Error
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JErrorPage.
 */
class JErrorPageTest extends TestCaseDatabase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * @covers  JErrorPage::render
	 */
	public function testEnsureTheErrorPageIsCorrectlyRendered()
	{
		$documentResponse = '<title>500 - Testing JErrorPage::render() with RuntimeException</title>Testing JErrorPage::render() with RuntimeException';

		$key = serialize(
			array(
				'error',
				array(
					'charset'   => 'utf-8',
					'lineend'   => 'unix',
					'tab'       => "\t",
					'language'  => 'en-GB',
					'direction' => 'ltr',
				),
			)
		);

		$mockErrorDocument = $this->getMockBuilder('JDocumentError')
			->setMethods(array('setError', 'setTitle', 'render'))
			->getMock();

		$mockErrorDocument->expects($this->any())
			->method('render')
			->willReturn($documentResponse);

		$mockFactory = $this->getMockBuilder(\Joomla\CMS\Document\FactoryInterface::class)->getMock();
		$mockFactory->method('createDocument')->willReturn($mockErrorDocument);

		// Set our mock document into the container
		$container = new \Joomla\DI\Container;
		$container->set(\Joomla\CMS\Document\FactoryInterface::class, $mockFactory);
		JFactory::$container = $container;

		// Create an Exception to inject into the method
		$exception = new RuntimeException('Testing JErrorPage::render() with RuntimeException', 500);

		// The render method echoes the output, so catch it in a buffer
		ob_start();
		JErrorPage::render($exception);
		$output = ob_get_clean();

		// Validate the mocked response from JDocument was received
		$this->assertEquals($documentResponse, $output);
	}

	/**
	 * @covers  JErrorPage::render
	 */
	public function testEnsureTheErrorPageIsCorrectlyRenderedWithThrowables()
	{
		$documentResponse = '<title>500 - Testing JErrorPage::render() with PHP 7 Error</title>Testing JErrorPage::render() with PHP 7 Error';

		$key = serialize(
			array(
				'error',
				array(
					'charset'   => 'utf-8',
					'lineend'   => 'unix',
					'tab'       => "\t",
					'language'  => 'en-GB',
					'direction' => 'ltr',
				),
			)
		);

		$mockErrorDocument = $this->getMockBuilder('JDocumentError')
			->setMethods(array('setError', 'setTitle', 'render'))
			->getMock();

		$mockErrorDocument->expects($this->any())
			->method('render')
			->willReturn($documentResponse);

		$mockFactory = $this->getMockBuilder(\Joomla\CMS\Document\FactoryInterface::class)->getMock();
		$mockFactory->method('createDocument')->willReturn($mockErrorDocument);

		// Set our mock document into the container
		$container = new \Joomla\DI\Container;
		$container->set(\Joomla\CMS\Document\FactoryInterface::class, $mockFactory);
		JFactory::$container = $container;

		// Create an Error to inject into the method
		$exception = new Error('Testing JErrorPage::render() with PHP 7 Error', 500);

		// The render method echoes the output, so catch it in a buffer
		ob_start();
		JErrorPage::render($exception);
		$output = ob_get_clean();

		// Validate the mocked response from JDocument was received
		$this->assertEquals($documentResponse, $output);
	}
}
