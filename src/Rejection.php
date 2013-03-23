<?php namespace Dotink\Lab {

	use Exception;
	use InvalidArgumentException;

	/**
	 * A simple assertion library
	 *
	 * @copyright Copyright (c) 2013, Matthew J. Sahagian
	 * @author Matthew J. Sahagian [mjs] <msahagian@dotink.org>
	 *
	 * @license Please reference the LICENSE.md file at the root of this distribution
	 *
	 * @package Lab
	 */
	class Rejection
	{
		private $assertion = NULL;

		/**
		 *
		 */
		public function __construct($value, $raw = FALSE)
		{
			$this->assertion = new Assertion($value, $raw);
		}

		/**
		 *
		 */
		public function __call($method, $args)
		{
			$assertion_methods = [
				'begins', 'contains', 'has', 'is', 'ends', 'equals', 'measures', 'throws'
			];

			if (in_array(strtolower($method), $assertion_methods)) {
				$rejection = new Assertion(function() use ($method, $args) {
					call_user_func_array([$this->assertion, $method], $args);
				});

				try {

					//
					// Throws will catch all exceptions by default.  Assertions in lab will throw
					// either a plain old Exception or an InvalidArgumentException.
					//

					$rejection->throws(['Exception', 'InvalidArgumentException']);

					//
					// Once we have ensured that our callback threw an exception.  We need to
					// see which type.  If it is a plain old exception then this is simply the
					// `throws()` method telling us that either the assertion did not throw an
					// exception, or that it threw an exception of a different type.
					//
					// If, on the other hand, we receive an InvalidArgumentException, then we
					// need to determine whether or not it was Lab that threw it.
					//

					$rejection->analyzeException(function($e) {
						$is_invalid_argument = get_class($e) == 'InvalidArgumentException';
						$exception_trace     = $e->getTrace();
						$throwing_class      = $exception_trace[0]['class'];
						$assertion_class     = __NAMESPACE__ . '\Assertion';

						if ($is_invalid_argument && $throwing_class == $assertion_class) {
							throw $e;
						}
					});

				} catch (InvalidArgumentException $e) {
					throw $e;

				} catch (Exception $e) {
					throw new Exception(sprintf(
						'Rejection Failed: %s',
						$this->assertion->alertSuccess()
					));
				}

			} else {
				call_user_func_array([$this->assertion, $method], $args);
			}

			return $this;
		}
	}
}
