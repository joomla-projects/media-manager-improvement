<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\User;

defined('JPATH_PLATFORM') or die;

/**
 * Wrapper class for UserHelper
 *
 * @since       3.4
 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
 */
class UserWrapper
{
	/**
	 * Helper wrapper method for addUserToGroup
	 *
	 * @param   integer  $userId   The id of the user.
	 * @param   integer  $groupId  The id of the group.
	 *
	 * @return  boolean  True on success
	 *
	 * @see     UserHelper::addUserToGroup()
	 * @since   3.4
	 * @throws  \RuntimeException
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function addUserToGroup($userId, $groupId)
	{
		return UserHelper::addUserToGroup($userId, $groupId);
	}

	/**
	 * Helper wrapper method for getUserGroups
	 *
	 * @param   integer  $userId  The id of the user.
	 *
	 * @return  array    List of groups
	 *
	 * @see     UserHelper::addUserToGroup()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function getUserGroups($userId)
	{
		return UserHelper::getUserGroups($userId);
	}

	/**
	 * Helper wrapper method for removeUserFromGroup
	 *
	 * @param   integer  $userId   The id of the user.
	 * @param   integer  $groupId  The id of the group.
	 *
	 * @return  boolean  True on success
	 *
	 * @see     UserHelper::removeUserFromGroup()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function removeUserFromGroup($userId, $groupId)
	{
		return UserHelper::removeUserFromGroup($userId, $groupId);
	}

	/**
	 * Helper wrapper method for setUserGroups
	 *
	 * @param   integer  $userId  The id of the user.
	 * @param   array    $groups  An array of group ids to put the user in.
	 *
	 * @return  boolean  True on success
	 *
	 * @see     UserHelper::setUserGroups()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function setUserGroups($userId, $groups)
	{
		return UserHelper::setUserGroups($userId, $groups);
	}

	/**
	 * Helper wrapper method for getProfile
	 *
	 * @param   integer  $userId  The id of the user.
	 *
	 * @return  object
	 *
	 * @see     UserHelper::getProfile()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function getProfile($userId = 0)
	{
		return UserHelper::getProfile($userId);
	}

	/**
	 * Helper wrapper method for activateUser
	 *
	 * @param   string  $activation  Activation string
	 *
	 * @return  boolean  True on success
	 *
	 * @see     UserHelper::activateUser()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function activateUser($activation)
	{
		return UserHelper::activateUser($activation);
	}

	/**
	 * Helper wrapper method for getUserId
	 *
	 * @param   string  $username  The username to search on.
	 *
	 * @return  integer  The user id or 0 if not found.
	 *
	 * @see     UserHelper::getUserId()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function getUserId($username)
	{
		return UserHelper::getUserId($username);
	}

	/**
	 * Helper wrapper method for hashPassword
	 *
	 * @param   string   $password   The plaintext password to encrypt.
	 * @param   integer  $algorithm  The hashing algorithm to use, represented by `PASSWORD_*` constants.
	 * @param   array    $options    The options for the algorithm to use.
	 *
	 * @return  string  The encrypted password.
	 *
	 * @see     UserHelper::hashPassword()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function hashPassword($password, $algorithm = PASSWORD_BCRYPT, array $options = array())
	{
		return UserHelper::hashPassword($password, $algorithm, $options);
	}

	/**
	 * Helper wrapper method for verifyPassword
	 *
	 * @param   string   $password  The plaintext password to check.
	 * @param   string   $hash      The hash to verify against.
	 * @param   integer  $user_id   ID of the user if the password hash should be updated
	 *
	 * @return  boolean  True if the password and hash match, false otherwise
	 *
	 * @see     UserHelper::verifyPassword()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function verifyPassword($password, $hash, $user_id = 0)
	{
		return UserHelper::verifyPassword($password, $hash, $user_id);
	}

	/**
	 * Helper wrapper method for genRandomPassword
	 *
	 * @param   integer  $length  Length of the password to generate
	 *
	 * @return  string  Random Password
	 *
	 * @see     UserHelper::genRandomPassword()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function genRandomPassword($length = 8)
	{
		return UserHelper::genRandomPassword($length);
	}

	/**
	 * Helper wrapper method for getShortHashedUserAgent
	 *
	 * @return  string  A hashed user agent string with version replaced by 'abcd'
	 *
	 * @see     UserHelper::getShortHashedUserAgent()
	 * @since   3.4
	 * @deprecated  4.0  Use `Joomla\CMS\User\UserHelper` directly
	 */
	public function getShortHashedUserAgent()
	{
		return UserHelper::getShortHashedUserAgent();
	}
}
