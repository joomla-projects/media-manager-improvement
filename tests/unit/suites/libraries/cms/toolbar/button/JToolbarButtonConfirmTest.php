<?php
/**
 * @package	    Joomla.UnitTest
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license	    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JToolbarButtonConfirm.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Toolbar
 * @since       3.0
 */
class JToolbarButtonConfirmTest extends TestCaseDatabase
{
	/**
	 * Toolbar object
	 *
	 * @var    JToolbar
	 * @since  3.0
	 */
	protected $toolbar;

	/**
	 * Object under test
	 *
	 * @var    JToolbarButtonConfirm
	 * @since  3.0
	 */
	protected $object;

	/**
	 * Backup of the SERVER superglobal
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected $backupServer;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function setUp()
	{
		$this->toolbar = JToolbar::getInstance();
		$this->object  = $this->toolbar->loadButtonType('confirm');

		$this->saveFactoryState();

		JFactory::$application = $this->getMockCmsApp();

		$this->backupServer = $_SERVER;

		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['SCRIPT_NAME'] = '';
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function tearDown()
	{
		$_SERVER = $this->backupServer;
		unset($this->backupServer, $this->toolbar, $this->object);

		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Tests the fetchButton method
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function testFetchButton()
	{
		$html = "<button id=\"toolbar-\" onclick=\"if (document.adminForm.boxchecked.value == 0) { Joomla.renderMessages({'error': [Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')]}) } else { if (confirm('Confirm action?')) { Joomla.submitbutton('article.save'); } }\" class=\"btn btn-sm btn-outline-danger\">\n"
			. "\t<span class=\"icon-confirm-test\" aria-hidden=\"true\"></span>\n"
			. "\tConfirm?</button>\n";


		$this->assertEquals(
			$html,
			$this->object->fetchButton('Confirm', 'Confirm action?', 'confirm-test', 'Confirm?', 'article.save')
		);
	}

	/**
	 * Tests the fetchId method
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function testFetchId()
	{
		$this->assertEquals(
			'toolbar-test',
			$this->object->fetchId('confirm', 'Message to render', 'test')
		);
	}
}
