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
 * Default factory for creating JDocument objects
 *
 * @since  4.0.0
 */
class Factory implements FactoryInterface
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
	public function createDocument(string $type = 'html', array $attributes = []): Document
	{
		$type  = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$ntype = null;

		$class = __NAMESPACE__ . '\\' . ucfirst($type) . 'Document';

		if (!class_exists($class))
		{
			$class = 'JDocument' . ucfirst($type);
		}

		if (!class_exists($class))
		{
			$ntype = $type;
			$class = RawDocument::class;
		}

		// Inject this factory into the document unless one was provided
		if (!isset($attributes['factory']))
		{
			$attributes['factory'] = $this;
		}

		/** @var Document $instance */
		$instance = new $class($attributes);

		if (!is_null($ntype))
		{
			// Set the type to the Document type originally requested
			$instance->setType($ntype);
		}

		return $instance;
	}

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
	public function createRenderer(Document $document, string $type): RendererInterface
	{
		// Determine the path and class
		$class = __NAMESPACE__ . '\\Renderer\\' . ucfirst($document->getType()) . '\\' . ucfirst($type) . 'Renderer';

		if (!class_exists($class))
		{
			$class = 'JDocumentRenderer' . ucfirst($document->getType()) . ucfirst($type);
		}

		if (!class_exists($class))
		{
			// "Legacy" class name structure
			$class = '\\JDocumentRenderer' . $type;

			if (!class_exists($class))
			{
				throw new \RuntimeException(sprintf('Unable to load renderer class %s', $type), 500);
			}
		}

		return new $class($document);
	}
}
