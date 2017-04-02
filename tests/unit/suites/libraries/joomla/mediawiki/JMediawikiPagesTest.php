<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Mediawiki
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JMediawikiPages.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Mediawiki
 */
class JMediawikiPagesTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var    JRegistry  Options for the Mediawiki object.
	 */
	protected $options;

	/**
	 * @var    JMediawikiHttp  Mock client object.
	 */
	protected $client;

	/**
	 * @var    JMediawikiPages  Object under test.
	 */
	protected $object;

	/**
	 * @var    string  Sample xml string.
	 */
	protected $sampleString = '<a><b></b><c></c></a>';

	/**
	 * @var    string  Sample xml error message.
	 */
	protected $errorString = '<message>Generic Error</message>';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->options = new JRegistry;
		$this->client = $this->getMockBuilder('JMediawikiHttp')->setMethods(array('get', 'post', 'delete', 'patch', 'put'))->getMock();

		$this->object = new JMediawikiPages(
			$this->options,
			$this->client
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 *
	 * @see     \PHPUnit\Framework\TestCase::tearDown()
	 * @since   3.6
	 */
	protected function tearDown()
	{
		unset($this->options);
		unset($this->client);
		unset($this->object);
	}

	/**
	 * Tests the getPageInfo method
	 */
	public function testGetPageInfo()
	{
		$returnData = $this->getReturnData();

		$this->client
			->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=info&titles=Main Page&format=xml')
			->willReturn($returnData);

		$this->assertEquals(
			simplexml_load_string($this->sampleString),
			$this->object->getPageInfo(array('Main Page'))
		);
	}

	/**
	 * Tests the getPageProperties method
	 */
	public function testGetPageProperties()
	{
		$returnData = $this->getReturnData();

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&prop=pageprops&titles=Main Page&format=xml')
			->willReturn($returnData);

		$this->assertEquals(
			simplexml_load_string($this->sampleString),
			$this->object->getPageProperties(array('Main Page'))
		);
	}

	/**
	 * Tests the getBackLinks method
	 */
	public function testGetBackLinks()
	{
		$returnData = $this->getReturnData();

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=backlinks&bltitle=Joomla&format=xml')
			->willReturn($returnData);

		$this->assertEquals(
			simplexml_load_string($this->sampleString),
			$this->object->getBackLinks('Joomla')
		);
	}

	/**
	 * Tests the getIWBackLinks method
	 */
	public function testGetIWBackLinks()
	{
		$returnData = $this->getReturnData();

		$this->client->expects($this->once())
			->method('get')
			->with('/api.php?action=query&list=iwbacklinks&iwbltitle=Joomla&format=xml')
			->willReturn($returnData);

		$this->assertEquals(
			simplexml_load_string($this->sampleString),
			$this->object->getIWBackLinks('Joomla')
		);
	}

	/**
	 * @return stdClass
	 */
	private function getReturnData()
	{
		$returnData = new stdClass;
		$returnData->code = 200;
		$returnData->body = $this->sampleString;

		return $returnData;
	}
}
