<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Access
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for \Joomla\Cms\Access\Rule.
 *
 * @package  Joomla.Platform
 *
 * @since    11.1
 */
class JAccessRuleTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Tests the \Joomla\Cms\Access\Rule::__construct and \Joomla\Cms\Access\Rule::__toString methods.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function test__construct()
	{
		$array = array(
			-42 => 1,
			2 => 1,
			3 => 0
		);

		// Get the string representation.
		$string = json_encode($array);

		// Test constructor with array.
		$rule1 = new \Joomla\Cms\Access\Rule($array);

		// Check that import equals export.
		$this->assertEquals(
			$string,
			(string) $rule1
		);

		// Test constructor with string.

		// Check that import equals export.

		// Check that import equals not export.

		$array_A = array(
			-44 => 1,
			2 => 1,
			3 => 0
		);

		$string_A = json_encode($array_A);
		$rule_A = new \Joomla\Cms\Access\Rule($string_A);
		$this->assertNotEquals(
			$string,
			(string) $rule_A
		);
	}

	/**
	 * Tests the \Joomla\Cms\Access\Rule::getData method.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function testGetData()
	{
		$array = array(
			-42 => 1,
			2 => 1,
			3 => 0
		);

		$rule = new \Joomla\Cms\Access\Rule($array);

		$this->assertEquals(
			$array,
			$rule->getData()
		);
	}

	/**
	 * Tests the \Joomla\Cms\Access\Rule::mergeIdentity method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testMergeIdentity()
	{
		// Construct an rule with no identities.
		$rule = new \Joomla\Cms\Access\Rule('');

		// Add the identity with allow.
		$rule->mergeIdentity(-42, true);
		$this->assertEquals(
			'{"-42":1}',
			(string) $rule
		);

		// Readd the identity, but deny.
		$rule->mergeIdentity(-42, false);
		$this->assertEquals(
			'{"-42":0}',
			(string) $rule
		);

		// Readd the identity with allow (checking deny wins).
		$rule->mergeIdentity(-42, true);
		$this->assertEquals(
			'{"-42":0}',
			(string) $rule
		);
	}

	/**
	 * Tests the \Joomla\Cms\Access\Rule::mergeIdentities method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testMergeIdentities()
	{
		$array = array(
			-42 => 1,
			2 => 1,
			3 => 0
		);

		// Construct an rule with no identities.
		$rule = new \Joomla\Cms\Access\Rule('');

		$rule->mergeIdentities($array);
		$this->assertEquals(
			json_encode($array),
			(string) $rule
		);

		// Check that import equals export.

		// Test testMergeIdentities with object

		$rule_A = new \Joomla\Cms\Access\Rule($array);
		$rule->mergeIdentities($rule_A);
		$this->assertEquals(
			json_encode($array),
			(string) $rule
		);

		$this->assertEquals(
			(string) $rule_A,
			(string) $rule
		);

		// Merge a new set, flipping some bits.
		$array = array(
			-42 => 0,
			2 => 1,
			3 => 1,
			4 => 1
		);

		// Ident 3 should remain false, 4 should be added.
		$result = array(
			-42 => 0,
			2 => 1,
			3 => 0,
			4 => 1
		);
		$rule->mergeIdentities($array);
		$this->assertEquals(
			json_encode($result),
			(string) $rule
		);
	}

	/**
	 * Tests the \Joomla\Cms\Access\Rule::allow method.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function testAllow()
	{
		// Simple allow and deny test.
		$array = array(
			-42 => 0,
			2 => 1
		);
		$rule = new \Joomla\Cms\Access\Rule($array);

		// This one should be denied.
		$this->assertFalse(
			$rule->allow(-42)
		);

		$this->assertEquals(null, $rule->allow(null));

		// This one should be allowed.
		$this->assertTrue(
			$rule->allow(2)
		);

		// This one should be denied.
		$this->assertFalse(
			$rule->allow(array(-42, 2))
		);
	}
}
