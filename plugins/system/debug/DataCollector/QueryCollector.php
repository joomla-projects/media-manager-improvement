<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Debug
 *
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Debug\DataCollector;

use DebugBar\DataCollector\AssetProvider;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Plugin\System\Debug\AbstractDataCollector;
use Joomla\Plugin\System\Debug\DebugMonitor;
use Joomla\Registry\Registry;

/**
 * QueryDataCollector
 *
 * @since  __DEPLOY_VERSION__
 */
class QueryCollector extends AbstractDataCollector implements AssetProvider
{
	/**
	 * Collector name.
	 *
	 * @var   string
	 * @since __DEPLOY_VERSION__
	 */
	private $name = 'queries';

	/**
	 * The query monitor.
	 *
	 * @var    DebugMonitor
	 * @since  __DEPLOY_VERSION__
	 */
	private $queryMonitor;

	/**
	 * Profile data.
	 *
	 * @var   array
	 * @since __DEPLOY_VERSION__
	 */
	private $profiles;

	/**
	 * Explain data.
	 *
	 * @var   array
	 * @since __DEPLOY_VERSION__
	 */
	private $explains;

	/**
	 * Accumulated Duration.
	 *
	 * @var   integer
	 * @since __DEPLOY_VERSION__
	 */
	private $accumulatedDuration = 0;

	/**
	 * Accumulated Memory.
	 *
	 * @var   integer
	 * @since __DEPLOY_VERSION__
	 */
	private $accumulatedMemory = 0;

	/**
	 * Constructor.
	 *
	 * @param   Registry      $params        Parameters.
	 * @param   DebugMonitor  $queryMonitor  Query monitor.
	 * @param   array         $profiles      Profile data.
	 * @param   array         $explains      Explain data
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function __construct(Registry $params, DebugMonitor $queryMonitor, array $profiles, array $explains)
	{
		$this->queryMonitor = $queryMonitor;

		parent::__construct($params);

		$this->profiles = $profiles;
		$this->explains = $explains;
	}

	/**
	 * Called by the DebugBar when data needs to be collected
	 *
	 * @since  __DEPLOY_VERSION__
	 *
	 * @return array Collected data
	 */
	public function collect(): array
	{
		// @todo fetch the database object in a non deprecated way..
		$database = Factory::$database;

		$statements = $this->getStatements();

		return [
			'data'       => [
				'statements'               => $statements,
				'nb_statements'            => \count($statements),
				'accumulated_duration_str' => $this->getDataFormatter()->formatDuration($this->accumulatedDuration),
				'memory_usage_str'         => $this->getDataFormatter()->formatBytes($this->accumulatedMemory),
				'xdebug_link'              => $this->getXdebugLinkTemplate(),
				'root_path'                => JPATH_ROOT
			],
			'count'      => \count($this->queryMonitor->getLog()),
		];
	}

	/**
	 * Returns the unique name of the collector
	 *
	 * @since  __DEPLOY_VERSION__
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * Returns a hash where keys are control names and their values
	 * an array of options as defined in {@see DebugBar\JavascriptRenderer::addControl()}
	 *
	 * @since  __DEPLOY_VERSION__
	 *
	 * @return array
	 */
	public function getWidgets(): array
	{
		return [
			'queries'       => [
				'icon'    => 'database',
				'widget'  => 'PhpDebugBar.Widgets.SQLQueriesWidget',
				'map'     => $this->name . '.data',
				'default' => '[]',
			],
			'queries:badge' => [
				'map'     => $this->name . '.count',
				'default' => 'null',
			],
		];
	}

	/**
	 * Assets for the collector.
	 *
	 * @since  __DEPLOY_VERSION__
	 *
	 * @return array
	 */
	public function getAssets(): array
	{
		return array(
			'css' => Uri::root(true) . '/media/plg_system_debug/widgets/sqlqueries/widget.min.css',
			'js' => Uri::root(true) . '/media/plg_system_debug/widgets/sqlqueries/widget.min.js'
		);
	}

	/**
	 * Prepare the executed statements data.
	 *
	 * @since  __DEPLOY_VERSION__
	 *
	 * @return array
	 */
	private function getStatements(): array
	{
		$statements    = [];
		$log           = $this->queryMonitor->getLog();
		$timings       = $this->queryMonitor->getTimings();
		$memoryLogs    = $this->queryMonitor->getMemoryLogs();
		$stacks        = $this->queryMonitor->getCallStacks();
		$collectStacks = $this->params->get('query_traces');

		foreach ($log as $id => $item)
		{
			$queryTime   = 0;
			$queryMemory = 0;

			if ($timings && isset($timings[$id * 2 + 1]))
			{
				// Compute the query time.
				$queryTime                 = ($timings[$id * 2 + 1] - $timings[$id * 2]);
				$this->accumulatedDuration += $queryTime;
			}

			if ($memoryLogs && isset($memoryLogs[$id * 2 + 1]))
			{
				// Compute the query memory usage.
				$queryMemory             = ($memoryLogs[$id * 2 + 1] - $memoryLogs[$id * 2]);
				$this->accumulatedMemory += $queryMemory;
			}

			$trace          = [];
			$callerLocation = '';

			if (isset($stacks[$id]))
			{
				$cnt = 0;

				foreach ($stacks[$id] as $i => $stack)
				{
					$class = $stack['class'] ?? '';
					$file  = $stack['file'] ?? '';
					$line  = $stack['line'] ?? '';

					$caller   = $this->formatCallerInfo($stack);
					$location = $file && $line ? "$file:$line" : 'same';

					$isCaller = 0;

					if (\Joomla\Database\DatabaseDriver::class === $class && false === strpos($file, 'DatabaseDriver.php'))
					{
						$callerLocation = $location;
						$isCaller       = 1;
					}

					if ($collectStacks)
					{
						$trace[] = [\count($stacks[$id]) - $cnt, $isCaller, $caller, $file, $line];
					}

					$cnt++;
				}
			}

			$statements[] = [
				'sql'          => $item,
				'duration_str' => $this->getDataFormatter()->formatDuration($queryTime),
				'memory_str'   => $this->getDataFormatter()->formatBytes($queryMemory),
				'caller'       => $callerLocation,
				'callstack'    => $trace,
				'explain'      => $this->explains[$id] ?? [],
				'profile'      => $this->profiles[$id] ?? [],
			];
		}

		return $statements;
	}
}
