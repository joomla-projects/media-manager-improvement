<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Media\Administrator\Event;

use Joomla\CMS\Event\AbstractEvent;

defined('_JEXEC') or die;

class OAuthCallbackEvent extends AbstractEvent
{
	public $result = array();

	private $context = null;

	private $parameters = null;

	/**
	 * @return null
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @param null $context
	 */
	public function setContext( $context ) {
		$this->context = $context;
	}

	/**
	 * @return null
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * @param null $parameters
	 */
	public function setParameters( $parameters ) {
		$this->parameters = $parameters;
	}

	/**
	 * @return array
	 */
	public function getResult(): array {
		return $this->result;
	}

	/**
	 * @param array $result
	 */
	public function setResult( array $result ) {
		$this->result = $result;
	}
}