<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Console;

defined('JPATH_PLATFORM') or die;

use Joomla\Console\AbstractCommand;
use Joomla\Session\SessionInterface;

/**
 * Console command for performing session garbage collection
 *
 * @since  4.0.0
 */
class SessionGcCommand extends AbstractCommand
{
	/**
	 * The session object.
	 *
	 * @var    SessionInterface
	 * @since  4.0.0
	 */
	private $session;

	/**
	 * Instantiate the command.
	 *
	 * @param   SessionInterface  $session  The session object.
	 *
	 * @since   4.0.0
	 */
	public function __construct(SessionInterface $session)
	{
		$this->session = $session;

		parent::__construct();
	}

	/**
	 * Execute the command.
	 *
	 * @return  integer  The exit code for the command.
	 *
	 * @since   4.0.0
	 */
	public function execute(): int
	{
		$symfonyStyle = $this->createSymfonyStyle();

		$symfonyStyle->title('Running Session Garbage Collection');

		if ($this->session->gc() === false)
		{
			$symfonyStyle->error('Garbage collection was not completed. Either the operation failed or is not supported on your platform.');

			return 1;
		}

		$symfonyStyle->success('Garbage collection completed.');

		return 0;
	}

	/**
	 * Initialise the command.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function initialise()
	{
		$this->setName('session:gc');
		$this->setDescription('Performs session garbage collection');
		$this->setHelp(
<<<EOF
The <info>%command.name%</info> command runs PHP's garbage collection operation for session data

<info>php %command.full_name%</info>
EOF
		);
	}
}
