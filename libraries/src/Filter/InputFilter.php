<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Filter;

defined('JPATH_PLATFORM') or die;

use Joomla\Database\UTF8MB4SupportInterface;
use Joomla\Filter\InputFilter as BaseInputFilter;

/**
 * InputFilter is a class for filtering input from any data source
 *
 * Forked from the php input filter library by: Daniel Morris <dan@rootcube.com>
 * Original Contributors: Gianpaolo Racca, Ghislain Picard, Marco Wandschneider, Chris Tobin and Andrew Eddie.
 *
 * @since  11.1
 */
class InputFilter extends BaseInputFilter
{
	/**
	 * A flag for Unicode Supplementary Characters (4-byte Unicode character) stripping.
	 *
	 * @var    integer
	 * @since  3.5
	 */
	public $stripUSC = 0;

	/**
	 * Constructor for inputFilter class. Only first parameter is required.
	 *
	 * @param   array    $tagsArray   List of user-defined tags
	 * @param   array    $attrArray   List of user-defined attributes
	 * @param   integer  $tagsMethod  WhiteList method = 0, BlackList method = 1
	 * @param   integer  $attrMethod  WhiteList method = 0, BlackList method = 1
	 * @param   integer  $xssAuto     Only auto clean essentials = 0, Allow clean blacklisted tags/attr = 1
	 * @param   integer  $stripUSC    Strip 4-byte unicode characters = 1, no strip = 0, ask the database driver = -1
	 *
	 * @since   11.1
	 */
	public function __construct($tagsArray = array(), $attrArray = array(), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1, $stripUSC = -1)
	{
		parent::__construct($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAuto);

		// Assign member variables
		$this->stripUSC = $stripUSC;

		/**
		 * If Unicode Supplementary Characters stripping is not set we have to check with the database driver. If the
		 * driver does not support USCs (i.e. there is no utf8mb4 support) we will enable USC stripping.
		 */
		if ($this->stripUSC === -1)
		{
			try
			{
				// Get the database driver
				$db = \JFactory::getDbo();

				if ($db instanceof UTF8MB4SupportInterface)
				{
					// This trick is required to let the driver determine the utf-8 multibyte support
					$db->connect();

					// And now we can decide if we should strip USCs
					$this->stripUSC = $db->hasUTF8mb4Support() ? 0 : 1;
				}
				else
				{
					$this->stripUSC = 1;
				}
			}
			catch (\RuntimeException $e)
			{
				// Could not connect to the database. Strip USC to be on the safe side.
				$this->stripUSC = 1;
			}
		}
	}

	/**
	 * Returns an input filter object, only creating it if it doesn't already exist.
	 *
	 * @param   array    $tagsArray   List of user-defined tags
	 * @param   array    $attrArray   List of user-defined attributes
	 * @param   integer  $tagsMethod  WhiteList method = 0, BlackList method = 1
	 * @param   integer  $attrMethod  WhiteList method = 0, BlackList method = 1
	 * @param   integer  $xssAuto     Only auto clean essentials = 0, Allow clean blacklisted tags/attr = 1
	 * @param   integer  $stripUSC    Strip 4-byte unicode characters = 1, no strip = 0, ask the database driver = -1
	 *
	 * @return  InputFilter  The InputFilter object.
	 *
	 * @since   11.1
	 */
	public static function &getInstance($tagsArray = array(), $attrArray = array(), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1, $stripUSC = -1)
	{
		$sig = md5(serialize(array($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAuto)));

		if (empty(self::$instances[$sig]))
		{
			self::$instances[$sig] = new InputFilter($tagsArray, $attrArray, $tagsMethod, $attrMethod, $xssAuto, $stripUSC);
		}

		return self::$instances[$sig];
	}

	/**
	 * Method to be called by another php script. Processes for XSS and
	 * specified bad code.
	 *
	 * @param   mixed   $source  Input string/array-of-string to be 'cleaned'
	 * @param   string  $type    The return type for the variable:
	 *                           INT:       An integer, or an array of integers,
	 *                           UINT:      An unsigned integer, or an array of unsigned integers,
	 *                           FLOAT:     A floating point number, or an array of floating point numbers,
	 *                           BOOLEAN:   A boolean value,
	 *                           WORD:      A string containing A-Z or underscores only (not case sensitive),
	 *                           ALNUM:     A string containing A-Z or 0-9 only (not case sensitive),
	 *                           CMD:       A string containing A-Z, 0-9, underscores, periods or hyphens (not case sensitive),
	 *                           BASE64:    A string containing A-Z, 0-9, forward slashes, plus or equals (not case sensitive),
	 *                           STRING:    A fully decoded and sanitised string (default),
	 *                           HTML:      A sanitised string,
	 *                           ARRAY:     An array,
	 *                           PATH:      A sanitised file path, or an array of sanitised file paths,
	 *                           TRIM:      A string trimmed from normal, non-breaking and multibyte spaces
	 *                           USERNAME:  Do not use (use an application specific filter),
	 *                           RAW:       The raw string is returned with no filtering,
	 *                           unknown:   An unknown filter will act like STRING. If the input is an array it will return an
	 *                                      array of fully decoded and sanitised strings.
	 *
	 * @return  mixed  'Cleaned' version of input parameter
	 *
	 * @since   11.1
	 */
	public function clean($source, $type = 'string')
	{
		// Strip Unicode Supplementary Characters when requested to do so
		if ($this->stripUSC)
		{
			// Alternatively: preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xE2\xAF\x91", $source) but it'd be slower.
			$source = $this->stripUSC($source);
		}

		return parent::clean($source, $type);
	}

	/**
	 * Function to punyencode utf8 mail when saving content
	 *
	 * @param   string  $text  The strings to encode
	 *
	 * @return  string  The punyencoded mail
	 *
	 * @since   3.5
	 */
	public function emailToPunycode($text)
	{
		$pattern = '/(("mailto:)+[\w\.\-\+]+\@[^"?]+\.+[^."?]+("|\?))/';

		if (preg_match_all($pattern, $text, $matches))
		{
			foreach ($matches[0] as $match)
			{
				$match  = (string) str_replace(array('?', '"'), '', $match);
				$text   = (string) str_replace($match, \JStringPunycode::emailToPunycode($match), $text);
			}
		}

		return $text;
	}

	/**
	 * Checks an uploaded for suspicious naming and potential PHP contents which could indicate a hacking attempt.
	 *
	 * The options you can define are:
	 * null_byte                   Prevent files with a null byte in their name (buffer overflow attack)
	 * forbidden_extensions        Do not allow these strings anywhere in the file's extension
	 * php_tag_in_content          Do not allow `<?php` tag in content
	 * shorttag_in_content         Do not allow short tag `<?` in content
	 * shorttag_extensions         Which file extensions to scan for short tags in content
	 * fobidden_ext_in_content     Do not allow forbidden_extensions anywhere in content
	 * php_ext_content_extensions  Which file extensions to scan for .php in content
	 *
	 * This code is an adaptation and improvement of Admin Tools' UploadShield feature,
	 * relicensed and contributed by its author.
	 *
	 * @param   array  $file     An uploaded file descriptor
	 * @param   array  $options  The scanner options (see the code for details)
	 *
	 * @return  boolean  True of the file is safe
	 *
	 * @since   3.4
	 */
	public static function isSafeFile($file, $options = array())
	{
		$defaultOptions = array(

			// Null byte in file name
			'null_byte'                  => true,

			// Forbidden string in extension (e.g. php matched .php, .xxx.php, .php.xxx and so on)
			'forbidden_extensions'       => array(
				'php', 'phps', 'pht', 'phtml', 'php3', 'php4', 'php5', 'php6', 'php7', 'inc', 'pl', 'cgi', 'fcgi', 'java', 'jar', 'py',
			),

			// <?php tag in file contents
			'php_tag_in_content'         => true,

			// <? tag in file contents
			'shorttag_in_content'        => true,

			// Which file extensions to scan for short tags
			'shorttag_extensions'        => array(
				'inc', 'phps', 'class', 'php3', 'php4', 'php5', 'txt', 'dat', 'tpl', 'tmpl',
			),

			// Forbidden extensions anywhere in the content
			'fobidden_ext_in_content'    => true,

			// Which file extensions to scan for .php in the content
			'php_ext_content_extensions' => array('zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz', 'jpa'),
		);

		$options = array_merge($defaultOptions, $options);

		// Make sure we can scan nested file descriptors
		$descriptors = $file;

		if (isset($file['name']) && isset($file['tmp_name']))
		{
			$descriptors = static::decodeFileData(
				array(
					$file['name'],
					$file['type'],
					$file['tmp_name'],
					$file['error'],
					$file['size'],
				)
			);
		}

		// Handle non-nested descriptors (single files)
		if (isset($descriptors['name']))
		{
			$descriptors = array($descriptors);
		}

		// Scan all descriptors detected
		foreach ($descriptors as $fileDescriptor)
		{
			if (!isset($fileDescriptor['name']))
			{
				// This is a nested descriptor. We have to recurse.
				if (!static::isSafeFile($fileDescriptor, $options))
				{
					return false;
				}

				continue;
			}

			$tempNames     = $fileDescriptor['tmp_name'];
			$intendedNames = $fileDescriptor['name'];

			if (!is_array($tempNames))
			{
				$tempNames = array($tempNames);
			}

			if (!is_array($intendedNames))
			{
				$intendedNames = array($intendedNames);
			}

			$len = count($tempNames);

			for ($i = 0; $i < $len; $i++)
			{
				$tempName     = array_shift($tempNames);
				$intendedName = array_shift($intendedNames);

				// 1. Null byte check
				if ($options['null_byte'])
				{
					if (strstr($intendedName, "\x00"))
					{
						return false;
					}
				}

				// 2. PHP-in-extension check (.php, .php.xxx[.yyy[.zzz[...]]], .xxx[.yyy[.zzz[...]]].php)
				if (!empty($options['forbidden_extensions']))
				{
					$explodedName = explode('.', $intendedName);
					$explodedName =	array_reverse($explodedName);
					array_pop($explodedName);
					$explodedName = array_map('strtolower', $explodedName);

					/*
					 * DO NOT USE array_intersect HERE! array_intersect expects the two arrays to
					 * be set, i.e. they should have unique values.
					 */
					foreach ($options['forbidden_extensions'] as $ext)
					{
						if (in_array($ext, $explodedName))
						{
							return false;
						}
					}
				}

				// 3. File contents scanner (PHP tag in file contents)
				if ($options['php_tag_in_content']
					|| $options['shorttag_in_content']
					|| ($options['fobidden_ext_in_content'] && !empty($options['forbidden_extensions'])))
				{
					$fp = @fopen($tempName, 'r');

					if ($fp !== false)
					{
						$data = '';

						while (!feof($fp))
						{
							$data .= @fread($fp, 131072);

							if ($options['php_tag_in_content'] && stristr($data, '<?php'))
							{
								return false;
							}

							if ($options['shorttag_in_content'])
							{
								$suspiciousExtensions = $options['shorttag_extensions'];

								if (empty($suspiciousExtensions))
								{
									$suspiciousExtensions = array(
										'inc', 'phps', 'class', 'php3', 'php4', 'txt', 'dat', 'tpl', 'tmpl',
									);
								}

								/*
								 * DO NOT USE array_intersect HERE! array_intersect expects the two arrays to
								 * be set, i.e. they should have unique values.
								 */
								$collide = false;

								foreach ($suspiciousExtensions as $ext)
								{
									if (in_array($ext, $explodedName))
									{
										$collide = true;

										break;
									}
								}

								if ($collide)
								{
									// These are suspicious text files which may have the short tag (<?) in them
									if (strstr($data, '<?'))
									{
										return false;
									}
								}
							}

							if ($options['fobidden_ext_in_content'] && !empty($options['forbidden_extensions']))
							{
								$suspiciousExtensions = $options['php_ext_content_extensions'];

								if (empty($suspiciousExtensions))
								{
									$suspiciousExtensions = array(
										'zip', 'rar', 'tar', 'gz', 'tgz', 'bz2', 'tbz', 'jpa',
									);
								}

								/*
								 * DO NOT USE array_intersect HERE! array_intersect expects the two arrays to
								 * be set, i.e. they should have unique values.
								 */
								$collide = false;

								foreach ($suspiciousExtensions as $ext)
								{
									if (in_array($ext, $explodedName))
									{
										$collide = true;

										break;
									}
								}

								if ($collide)
								{
									/*
									 * These are suspicious text files which may have an executable
									 * file extension in them
									 */
									foreach ($options['forbidden_extensions'] as $ext)
									{
										if (strstr($data, '.' . $ext))
										{
											return false;
										}
									}
								}
							}

							/*
							 * This makes sure that we don't accidentally skip a <?php tag if it's across
							 * a read boundary, even on multibyte strings
							 */
							$data = substr($data, -10);
						}

						fclose($fp);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to decode a file data array.
	 *
	 * @param   array  $data  The data array to decode.
	 *
	 * @return  array
	 *
	 * @since   3.4
	 */
	protected static function decodeFileData(array $data)
	{
		$result = array();

		if (is_array($data[0]))
		{
			foreach ($data[0] as $k => $v)
			{
				$result[$k] = static::decodeFileData(array($data[0][$k], $data[1][$k], $data[2][$k], $data[3][$k], $data[4][$k]));
			}

			return $result;
		}

		return array('name' => $data[0], 'type' => $data[1], 'tmp_name' => $data[2], 'error' => $data[3], 'size' => $data[4]);
	}

	/**
	 * Try to convert to plaintext
	 *
	 * @param   string  $source  The source string.
	 *
	 * @return  string  Plaintext string
	 *
	 * @since   3.5
	 */
	protected function decode($source)
	{
		static $ttr;

		if (!is_array($ttr))
		{
			// Entity decode
			$trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'ISO-8859-1');

			foreach ($trans_tbl as $k => $v)
			{
				$ttr[$v] = utf8_encode($k);
			}
		}

		$source = strtr($source, $ttr);

		// Convert decimal
		$source = preg_replace_callback('/&#(\d+);/m', function($m)
		{
			return utf8_encode(chr($m[1]));
		}, $source
		);

		// Convert hex
		$source = preg_replace_callback('/&#x([a-f0-9]+);/mi', function($m)
		{
			return utf8_encode(chr('0x' . $m[1]));
		}, $source
		);

		return $source;
	}

	/**
	 * Recursively strip Unicode Supplementary Characters from the source. Not: objects cannot be filtered.
	 *
	 * @param   mixed  $source  The data to filter
	 *
	 * @return  mixed  The filtered result
	 *
	 * @since  3.5
	 */
	protected function stripUSC($source)
	{
		if (is_object($source))
		{
			return $source;
		}

		if (is_array($source))
		{
			$filteredArray = array();

			foreach ($source as $k => $v)
			{
				$filteredArray[$k] = $this->stripUSC($v);
			}

			return $filteredArray;
		}

		return preg_replace('/[\xF0-\xF7].../s', "\xE2\xAF\x91", $source);
	}
}
