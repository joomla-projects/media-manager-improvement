<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Document;

defined('_JEXEC') or die;

/**
 * Interface defining a factory which can create JDocument objects
 *
 * @since  4.0.0
 */
interface FactoryInterface
{
	/**
	 * Creates a new JDocument object for the requested format.
	 *
	 * @param   string  $type        The document type to instantiate
	 * @param   array   $attributes  Array of attributes
	 *
	 * @return  Document
	 *
	 * @since   4.0.0
	 */
	public function createDocument(string $type = 'html', array $attributes = []): Document;

	/**
	 * Creates a new renderer object.
	 *
	 * @param   Document  $document  The JDocument instance to attach to the renderer
	 * @param   string    $type      The renderer type to instantiate
	 *
	 * @return  RendererInterface
	 *
	 * @since   4.0.0
	 */
	public function createRenderer(Document $document, string $type): RendererInterface;
}
