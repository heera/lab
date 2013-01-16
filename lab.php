<?php namespace Dotink\Lab {

	include(__DIR__ . '/library/parody/Load.php');
	include(__DIR__ . '/library/Assertion.php');

	use Dotink\Parody;

	const DS                  = \DIRECTORY_SEPARATOR;
	const LB                  = \PHP_EOL;
	const TAB                 = "\t";

	//
	// Some comparison constants
	//

	const GT  = '>';
	const LT  = '<';
	const GTE = '>=';
	const LTE = '<=';

	const EVEN = 1;
	const ODD  = 0;

	const EXACTLY = TRUE;

	const REGEX_ABSOLUTE_PATH = '#^(/|\\\\|[a-z]:(\\\\|/)|\\\\|//)#i';

	/**
	 * Get a message printed in a particular color
	 *
	 * @param string $message The Message to print
	 * @param string $color The color to print it in
	 * @return string The colored message for CLI output
	 */
	function _($message, $color) {
		$colors = [
			'black'        => '0;30',
			'dark_gray'    => '1;30',
			'blue'         => '0;34',
			'light_blue'   => '1;34',
			'green'        => '0;32',
			'light_green'  => '1;32',
			'cyan'         => '0;36',
			'light_cyan'   => '1;36',
			'red'          => '0;31',
			'light_red'    => '1;31',
			'purple'       => '0;35',
			'light_purple' => '1;35',
			'brown'        => '0;33',
			'yellow'       => '1;33',
			'light_gray'   => '0;37',
			'white'        => '1;37'
		];

		return sprintf("\033[%sm%s\033[0m", $colors[$color], $message);
	}


	/**
	 *
	 */
	function assert($value, $raw = FALSE)
	{
		return new Assertion($value, $raw);
	}


	/**
	 * Gets the configuration for the system
	 *
	 * @return array The system configuration
	 */
	function get_config()
	{
		try {
			for (
				$config_path = 'lab.config';
				!is_readable(__DIR__ . DS . $config_path);
				$config_path = '..' . DS . $config_path
			);

			return needs(realpath(__DIR__ . DS . $config_path));

		} catch (\Exception $e) {
			echo _('Config Failed: ', 'red');
			echo $e->getMessage() . LB;
			echo LB;
			exit(-1);
		}
	}


	/**
	 * Depend on a file being included
	 *
	 * @param string $file The file to depend on
	 * @return mixed The return result from including the file
	 */
	function needs($file)
	{
		if (!is_readable($file)) {
			throw new \Exception(sprintf(
				'Cannot include %s, file is not readable',
				$file
			));
		}

		exec(PHP_BINARY . ' -l ' . escapeshellarg($file), $output);

		$output = implode("\n", $output);

		if (strpos($output, 'Parse error') !== FALSE) {
			throw new \Exception(sprintf(
				'Cannot include %s, syntax error',
				$output
			));
		}

		return include $file;
	}


	/**
	 * Adds tests from a directory
	 *
	 * @param string $directory
	 */
	function add_tests($directory)
	{
		$fixtures = array();
		$fixtures = array_merge($fixtures, glob($directory . DS . '*.php'));

		foreach (glob($directory . DS . '*', GLOB_ONLYDIR) as $sub_directory) {
			$fixtures = array_merge($fixtures, add_tests($sub_directory));
		}

		return $fixtures;
	}

	/**
	 * Execute Fixtures and Tests.  If no argument was passed to lab, then we will scan the
	 * configured testDirectory.  Otherwise will will execute the single fixture available in the
	 * file path passed as the first argument.
	 *
	 * @return void
	 */
	call_user_func(function() use($argv) {

		$config         = get_config();
		$test_directory = !preg_match(REGEX_ABSOLUTE_PATH, $config['tests_directory'])
			? realpath(__DIR__ . DS . $config['tests_directory'])
			: $config['tests_directory'];


		if (!isset($argv[1])) {

			$banner = (
				' _        _    ____          _   ___  ' . LB .
				'| |      / \  | __ )  __   _/ | / _ \ ' . LB .
				'| |     / _ \ |  _ \  \ \ / / || | | |' . LB .
				'| |___ / ___ \| |_) |  \ V /| || |_| |' . LB .
				'|_____/_/   \_\____/    \_/ |_(_)___/  By: Dotink'
			);

			echo _($banner, 'dark_gray') . LB . LB;

			$fixtures = add_tests($test_directory);

			foreach ($fixtures as $fixture) {
				passthru(
					PHP_BINARY . ' -d display_errors=Off ' . __FILE__ . ' ' . escapeshellarg($fixture),
					$status
				);

				if ($status !== 0) {
					exit(-1);
				}
			}

			return;
		}

		$config   = get_config();
		$fixture  = require($argv[1]);
		$file     = str_replace($test_directory . DS, '', $argv[1]);
		$headline = sprintf('Running %s', pathinfo($file, PATHINFO_FILENAME));
		$errors   = array();

		set_error_handler(function($number, $string, $file, $line, $context) use (&$errors) {
			$type = 'Unknown(' . $number . ')';

			switch ($number) {
				case E_ERROR:   $type = 'E_ERROR';   break;
				case E_WARNING: $type = 'E_WARNING'; break;
				case E_NOTICE:  $type = 'E_NOTICE';  break;
				case E_STRICT:  $type = 'E_STRICT';  break;
			}

			$errors[] = sprintf(
				'[%s](%s:%d): %s' . LB,
				$type, $file, $line, $string
			);
		});

		register_shutdown_function(function() {

		});

		echo _($headline, 'blue') . LB;

		try {
			if (isset($config['setup']) && $config['setup'] instanceof \Closure) {
				call_user_func($config['setup'], $config);
			}

			if (isset($fixture['setup']) && $fixture['setup'] instanceof \Closure) {
				call_user_func($fixture['setup'], $config);
			}
		} catch (\Exception $e) {
			echo _('Setup Failed: ', 'red');
			echo $e->getMessage() . LB;
			echo LB;
			exit(-1);
		}

		if (isset($fixture['tests']) && is_array($fixture['tests'])) {
			foreach ($fixture['tests'] as $message => $test) {
				if ($test instanceof \Closure) {
					echo TAB . ' - ' . $message . ' ';

					try {
						call_user_func($test, $config);
						echo '[' . _('PASS', 'green') . ']' . LB;

					} catch (\Exception $e) {
						echo '[' . _('FAIL', 'red')   . ']' . LB;
						echo LB;
						echo $e->getMessage() . LB;
						echo LB;
						echo 'PHP Errors (' . count($errors) . ')' . LB;
						echo LB;
						foreach ($errors as $error) {
							echo $error . LB;
						}
						echo LB;
						exit(-1);
					}
				}
			}
		}

		try {
			if (isset($fixture['cleanup']) && $fixture['cleanup'] instanceof \Closure) {
				call_user_func($fixture['cleanup'], $config);
			}

			if (isset($config['cleanup']) && $config['cleanup'] instanceof \Closure) {
				call_user_func($config['cleanup'], $config);
			}
		} catch (\Exception $e) {
			echo _('Cleanup Failed: ', 'red');
			echo $e->getMessage() . LB;
			echo LB;
			exit(-1);
		}
	});
}
