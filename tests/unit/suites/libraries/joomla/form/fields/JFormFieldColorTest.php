<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

JFormHelper::loadFieldClass('color');

/**
 * Test class for JFormFieldColor.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Form
 * @since       12.1
 */
class JFormFieldColorTest extends TestCaseDatabase
{
	/**
	 * $_SERVER variable
	 *
	 * @var   array
	 */
	protected $server;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();
		$this->server = $_SERVER;
		$_SERVER['REQUEST_METHOD'] = 'get';
		$_SERVER['HTTP_HOST'] = 'mydomain.com';
		$_SERVER['SCRIPT_NAME'] = '/';

		JFactory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();
		$_SERVER = $this->server;
		JUri::reset();

		parent::tearDown();
	}

	/**
	 * Test the getInput method.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public function testGetInput()
	{
		$form = new JForm('form1');

		$this->assertThat(
			$form->load('<form><field name="color" type="color" disabled="true" onchange="window.reload()" class="inputbox" /></form>'),
			$this->isTrue(),
			'Line:' . __LINE__ . ' XML string should load successfully.'
		);

		$field = new JFormFieldColor($form);

		$this->assertThat(
			$field->setup($form->getXml()->field, 'value'),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$this->assertThat(
			strlen($field->input),
			$this->greaterThan(0),
			'Line:' . __LINE__ . ' The getInput method should return something without error.'
		);
	}
}
