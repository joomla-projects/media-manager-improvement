<?php
/**
 * @package    Joomla.Test
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Abstract test case class for MySQL database testing with the PDO based driver.
 *
 * @package  Joomla.Test
 * @since    3.4
 */
abstract class TestCaseDatabasePdomysql extends TestCaseDatabase
{
	/**
	 * @var    JDatabaseDriverPdomysql  The active database driver being used for the tests.
	 * @since  3.4
	 */
	protected static $driver;

	/**
	 * @var    array  The JDatabaseDriver options for the connection.
	 * @since  3.4
	 */
	private static $_options = array('driver' => 'pdomysql');

	/**
	 * @var    JDatabaseDriverPdomysql  The saved database driver to be restored after these tests.
	 * @since  3.4
	 */
	private static $_stash;

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * An example DSN would be: host=localhost;dbname=joomla_ut;user=utuser;pass=ut1234
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public static function setUpBeforeClass()
	{
		// First let's look to see if we have a DSN defined or in the environment variables.
		if (!defined('JTEST_DATABASE_PDO_MYSQL_DSN') && !getenv('JTEST_DATABASE_PDO_MYSQL_DSN'))
		{
			static::markTestSkipped('The PDO MySQL driver is not configured.');
		}

		$dsn = defined('JTEST_DATABASE_PDO_MYSQL_DSN') ? JTEST_DATABASE_PDO_MYSQL_DSN : getenv('JTEST_DATABASE_PDO_MYSQL_DSN');

		// First let's trim the mysql: part off the front of the DSN if it exists.
		if (strpos($dsn, 'mysql:') === 0)
		{
			$dsn = substr($dsn, 6);
		}

		// Split the DSN into its parts over semicolons.
		$parts = explode(';', $dsn);

		// Parse each part and populate the options array.
		foreach ($parts as $part)
		{
			list ($k, $v) = explode('=', $part, 2);

			switch ($k)
			{
				case 'host':
					self::$_options['host'] = $v;
					break;
				case 'dbname':
					self::$_options['database'] = $v;
					break;
				case 'user':
					self::$_options['user'] = $v;
					break;
				case 'pass':
					self::$_options['password'] = $v;
					break;
				case 'charset':
					self::$_options['charset'] = $v;
					break;
			}
		}

		try
		{
			// Attempt to instantiate the driver.
			static::$driver = JDatabaseDriver::getInstance(self::$_options);
		}
		catch (RuntimeException $e)
		{
			static::$driver = null;
		}

		// If for some reason an exception object was returned set our database object to null.
		if (static::$driver instanceof Exception)
		{
			static::$driver = null;
		}

		// Setup the factory pointer for the driver and stash the old one.
		self::$_stash = JFactory::$database;
		JFactory::$database = static::$driver;
	}

	/**
	 * This method is called after the last test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public static function tearDownAfterClass()
	{
		JFactory::$database = self::$_stash;

		if (static::$driver !== null)
		{
			static::$driver->disconnect();
			static::$driver = null;
		}
	}

	/**
	 * Returns the default database connection for running the tests.
	 *
	 * @return  PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 *
	 * @since   3.4
	 */
	protected function getConnection()
	{
		if (static::$driver === null)
		{
			static::fail('Could not fetch a database driver to establish the connection.');
		}

		static::$driver->connect();

		return $this->createDefaultDBConnection(static::$driver->getConnection(), self::$_options['database']);
	}
}
