<?php namespace Dotink\Lab {

	class Assertion
	{
		const REGEX_PHP_METHOD = '/
			[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]* # Class
			\:\:                                     # Separator
			[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]* # Method
		/x';


		const REGEX_PHP_PROPERTY = '/
			[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*   # Class
			\:\:                                       # Separator
			\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]* # Property
		/x';


		/**
		 * Arguments held for callable assertions
		 *
		 * @access private
		 * @var array
		 */
		private $args = array();


		/**
		 * Whether not the assertion is a Class
		 *
		 * @access private
		 * @var boolean
		 */
		private $isClass = FALSE;


		/**
		 * Whether not the assertion is a boolean
		 *
		 * @access private
		 * @var boolean
		 */
		private $isBoolean = FALSE;


		/**
		 * Whether not the assertion is a closure
		 *
		 * @access private
		 * @var boolean
		 */
		private $isClosure = FALSE;


		/**
		 * Whether not the assertion is a function
		 *
		 * @access private
		 * @var boolean
		 */
		private $isFunction = FALSE;


		/**
		 * Whether not the assertion is a method
		 *
		 * @access private
		 * @var boolean
		 */
		private $isMethod = FALSE;


		/**
		 * Whether not the assertion is numeric (float or integer)
		 *
		 * @access private
		 * @var boolean
		 */
		private $isNumber = FALSE;


		/**
		 * Whether not the assertion is an object
		 *
		 * @access private
		 * @var boolean
		 */
		private $isObject = FALSE;


		/**
		 * Whether not the assertion is a property
		 *
		 * @access private
		 * @var boolean
		 */
		private $isProperty = FALSE;


		/**
		 * Whether not the assertion is a string
		 *
		 * @access private
		 * @var boolean
		 */
		private $isString = FALSE;


		/**
		 * Whether or not the assertion needs an object (such as for object methods/properties)
		 *
		 * @access private
		 * @var boolean
		 */
		private $needsObject = FALSE;


		/**
		 * The PHP determined type of the value
		 *
		 * @access private
		 * @var mixed
		 */
		private $type = NULL;


		/**
		 * The original value of the assertion
		 *
		 * @access private
		 * @var mixed
		 */
		private $value = NULL;



		private $class = NULL;
		private $method = NULL;
		private $object = NULL;
		private $property = NULL;


		/**
		 * Create a new assertion, this will determine much about the nature of our value
		 *
		 * @access public
		 * @param mixed $value The value to assert
		 * @param boolean $raw Whether we should disable special interpretation, default FALSE
		 * @return void
		 */
		public function __construct($value, $raw = FALSE)
		{
			$this->value = $value;
			$this->type  = gettype($value);

			switch ($this->type) {
				case 'string':
					$this->loadString($raw);
					break;

				case 'object':
					$this->loadObject();
					break;

				case 'array':
					$this->loadArray();
					break;

				case 'integer':
				case 'float':
					$this->loadNumber();
					break;

				case 'boolean':
					$this->loadBoolean();
					break;

				default:
					throw new \Exception(sprintf(
						'Cannot assert type %s, not supported',
						$this->type
					));
			}
		}


		/**
		 * Asserts that one or more values is contained in the result
		 *
		 * @access public
		 * @param mixed $value A value to check for in the result
		 * @param ...
		 * @return Assertion The original assertion for method chaining
		 */
		public function contains($value)
		{
			$result  = $this->resolve();
			$values  = func_get_args();
			$missing = array();

			if (is_array($result) || (is_object($result) && $result instanceof \ArrayAccess)) {
				foreach ($values as $value) {
					if (array_search($value, $result) === FALSE) {
						$missing[] = $value;
					}
				}

				if (count($missing)) {
					throw new \Exception(sprintf(
						'Assertion failed, %d of the values could not be found in result %s',
						count($missing),
						$this->formatValue($result)
					));
				}

				return $this;
			}

			throw new \Exception(sprintf(
				'Cannot use contains() on assertion of type "%s"',
				gettype($result)
			));
		}


		/**
		 * Asserts that the result begins with a certain value
		 *
		 * @access public
		 * @param mixed $beginning A value equal to the beginning
		 * @return Assertion The original assertion for method chaining
		 */
		public function begins($beginning)
		{
			$result = $this->resolve();

			if (is_string($result)) {
				$encoding           = mb_detect_encoding($result . 'e', 'UTF-8, ISO-8859-1');
				$beginning_encoding = mb_detect_encoding($result . 'e', 'UTF-8, ISO-8859-1');

				if ($encoding != $beginning_encoding) {
					$encoding = $beginning_encoding;
				}

				$size   = mb_strlen($result, $encoding);
				$length = mb_strlen($beginning, $encoding);

				if ($size >= $length) {
					if ($beginning != mb_substr($result, 0, $length)) {
						throw new \Exception(sprintf(
							'Assertion failed, result %s does not begin with %s',
							$this->formatValue($result),
							$this->formatValue($beginning)
						));
					}

					return $this;
				}

				throw new \Exception(sprintf(
					'Assertion failed, result %s is not long enough to begin with %s',
					$this->formatValue($result),
					$this->formatValue($beginning)
				));
			}

			throw new \Exception(sprintf(
				'Cannot use begins() on assertion of type "%s"',
				gettype($result)
			));
		}


		/**
		 * Asserts that the result ends with a certain value
		 *
		 * @access public
		 * @param mixed $end A value equal to the ending
		 * @return Assertion The original assertion for method chaining
		 */
		public function ends($end)
		{
			$result = $this->resolve();

			if (is_string($result)) {
				$encoding     = mb_detect_encoding($result . 'e', 'UTF-8, ISO-8859-1');
				$end_encoding = mb_detect_encoding($result . 'e', 'UTF-8, ISO-8859-1');

				if ($encoding != $end_encoding) {
					$encoding = $end_encoding;
				}

				$length = mb_strlen($result, $encoding);
				$start  = mb_strlen($end, $encoding);

				if ($length >= $start) {
					if ($end != mb_substr($result, $length - $start)) {
						throw new \Exception(sprintf(
							'Assertion failed, result %s does not end with %s',
							$this->formatValue($result),
							$this->formatValue($end)
						));
					}

					return $this;
				}

				throw new \Exception(sprintf(
					'Assertion failed, result %s is not long enough to end with %s',
					$this->formatValue($result),
					$this->formatValue($end)
				));
			}

			throw new \Exception(sprintf(
				'Cannot use ends() on assertion of type "%s"',
				gettype($result)
			));
		}


		/**
		 * Asserts that the result is equal to a value
		 *
		 * @access public
		 * @param mixed $value The value to check for equality
		 * @param boolean $exactly Whether or not the comparision should be exact
		 * @return Assertion The original assertion for method chaining
		 */
		public function equals($value, $exactly = FALSE)
		{
			$result    = $this->resolve();
			$condition = $exactly ? ($result === $value) : ($result == $value);

			if ($condition) {
				return $this;
			}

			throw new \Exception(sprintf(
				'Assertion failed, expected %s%s but got %s',
				$this->formatValue($value),
				$exactly ? ' (exactly)' : NULL,
				$this->formatValue($result)
			));
		}


		/**
		 * Asserts that the result has a given key or keys
		 *
		 * @access public
		 * @param mixed $key A key to check for
		 * @param ...
		 * @return Assertion The original assertion for method chaining
		 */
		public function has($key)
		{
			$result  = $this->resolve();
			$keys    = func_get_args();
			$missing = array();

			if (is_array($result) || (is_object($result) && $result instanceof \ArrayAccess)) {
				foreach ($keys as $key) {
					if (!(is_string($key) || is_int($key))) {
						throw new \Exception(sprintf(
							'Assertion failed, invalid key %s provided to has()',
							$this->formatValue($key)
						));
					}

					if (!array_key_exists($key, $result)) {
						$missing[] = $key;
					}
				}

				if ($num_missing = count($missing)) {
					throw new \Exception(sprintf(
						'Assertion failed, value %s is missing %s out of %s keys',
						$this->formatValue($result),
						$num_missing,
						count($keys)
					));
				}

				return $this;
			}

			throw new \Exception(sprintf(
				'Cannot use has() on assertion of non-array-accessible type "%s"',
				gettype($result)
			));
		}


		/**
		 * Asserts that the length/size of the result measures to a certain number
		 *
		 * @access public
		 * @param string $condition An optional condition: GT, LT, GTE, LTE
		 * @param int $size The size to compare to
		 * @return Assertion The original assertion for method chaining
		 */
		public function measures($size)
		{
			$result   = $this->resolve();
			$length   = NULL;
			$modifier = NULL;

			if (func_num_args() == 2) {
				$modifier = func_get_arg(0);
				$size     = func_get_arg(1);
			}


			if (is_array($result) || (is_object($result) && $result instanceof \Countable)) {
				$length = count($result);
			} elseif (is_string($result)) {
				$encoding = mb_detect_encoding($result . 'e', 'UTF-8, ISO-8859-1, ASCII');
				$length   = mb_strlen($result, $encoding);
			}

			if (isset($length)) {
				switch ($modifier) {
					case GT:  $condition = $length >  $size; break;
					case LT:  $condition = $length <  $size; break;
					case GTE: $condition = $length >= $size; break;
					case LTE: $condition = $length <= $size; break;
					default:  $condition = $length == $size; break;
				}

				if (!$condition) {
					throw new \Exception(sprintf(
						'Assertion failed, value %s measures %d instead of %s%d',
						$this->formatValue($result),
						$length,
						$modifier ? ($modifier . ' ') : NULL,
						$size
					));
				}

				return $this;
			}

			throw new \Exception(sprintf(
				'Cannot use measures() on non countable or sizeable assertion of type "%s"',
				gettype($result)
			));
		}


		/**
		 * Tests the current assertion to see if it throws an exception
		 *
		 * @access public
		 * @param string $class The exception class to test for
		 * @return Assertion The original assertion for method chaining
		 */
		public function throws($class)
		{
			if (!($this->isMethod || $this->isFunction || $this->isClosure)) {
				throw new \Exception(sprintf(
					'Cannot assert that non-callable value %s throws an exception',
					$this->formatValue($this->value)
				));
			}

			try {
				$result = $this->resolve();

			} catch (\Exception $e) {
				$exception_class = get_class($e);

				if ($exception_class == $class) {
					return $this;
				} else {
					throw new \Exception(sprintf(
						'Assertion failed, callable %s threw "%s" instead of "%s" (%s)',
						$this->value,
						$exception_class,
						$class,
						$e->getMessage()
					));
				}
			}

			throw new \Exception(sprintf(
				'Assertion failed, callable %s returned %s instead of throwing "%s"',
				$this->value,
				$this->formatValue($result),
				$class
			));
		}


		/**
		 * Provide an object to use for assertions which require an object
		 *
		 * @access public
		 * @param object $object The object to use
		 * @return Assertion The assertion, for method chaining
		 */
		public function using($object)
		{
			if (!$this->needsObject) {
				throw new \Exception(sprintf(
					'Cannot assert using() on static "%s"',
					$this->value
				));

			} elseif (!is_object($object)) {
				throw new \Exception(sprintf(
					'Cannot assert "%s" using() non-object %s',
					$this->value,
					$this->formatValue($object)
				));

			} elseif (get_class($object) != $this->class) {
				throw new \Exception(sprintf(
					'Cannot assert "%s" using() object of class "%s"',
					$this->value,
					$this->class
				));
			}

			$this->object = $object;

			return $this;
		}


		/**
		 * Provide arguments for assertions which are callable
		 *
		 * @access public
		 * @param mixed $arg The first argument
		 * @param ...
		 * @return Assertion The assertion, for method chaining
		 */
		public function with()
		{
			if (!($this->isMethod || $this->isFunction || $this->isClosure)) {
				throw new \Exception(sprintf(
					'Cannot assert with() on non-callable %s',
					$this->formatValue($this->value)
				));
			}

			$this->args = func_get_args();

			return $this;
		}



		/**
		 * Checks whether or not an assertion requiring an object needs ones.
		 *
		 * @access private
		 * @return boolean TRUE if an object is needed and available, FALSE otherwise
		 */
		private function checkObject()
		{
			if (($this->isMethod || $this->isProperty) && $this->needsObject) {
				if ($this->object !== NULL) {
					return TRUE;
				}

				if ($this->isMethod) {
					throw new \Exception(sprintf(
						'Cannot assert non-static method "%s" without using() an object',
						$this->method
					));

				} else {
					throw new \Exception(sprintf(
						'Cannot assert non-static property "%s" without using() an object',
						$this->property
					));
				}
			}

			return FALSE;
		}


		/**
		 * All the requisite logic for loading an array assertion
		 *
		 * @access private
		 * @return void
		 */
		private function loadArray()
		{
			$this->isArray           = TRUE;
		}


		/**
		 * All the requisite logic for loading a boolean assertion
		 *
		 * @access private
		 * @return void
		 */
		private function loadBoolean()
		{
			$this->isBoolean = TRUE;
		}


		/**
		 * All the requisite logic for loading a numeric assertion
		 *
		 * @access private
		 * @return void
		 */
		private function loadNumber()
		{
			$this->isNumber = TRUE;
		}


		/**
		 * All the requisite logic for loading a string assertion
		 *
		 * @access private
		 * @param boolean $raw Whether or not we should try special interpretations
		 * @return void;
		 */
		private function loadString($raw)
		{
			if ($raw) {
				$this->isString = TRUE;

			} else {
				if (strpos($this->value, '::') !== FALSE) {
					if (preg_match(self::REGEX_PHP_METHOD, $this->value)) {
						$this->reflectMethod();
					} elseif (preg_match(self::REGEX_PHP_PROPERTY, $this->value)) {
						$this->reflectProperty();
					}

				} elseif (class_exists($this->value)) {
					$this->isClass = TRUE;
					$this->class   = $this->value;

				} elseif (function_exists($this->value)) {
					$this->isFunction = TRUE;
					$this->call       = function($args) {
						return call_user_func_array($this->value, $args);
					};

				} else {
					$this->isString = TRUE;
				}
			}
		}


		/**
		 * All the requisite logic for loading an object assertion
		 *
		 * @access private
		 * @return void
		 */
		private function loadObject() {
			$this->isObject = TRUE;
			$this->class    = get_class($this->value);
			$this->object   = $this->value;
		}


		/**
		 * Formats a value somewhat neatly (depending on type) into a printable string
		 *
		 * @access private
		 * @param mixed $value The value to format
		 * @return string A hopefully nice string represenation of the original value
		 */
		private function formatValue($value)
		{
			$detail = NULL;

			switch($type = gettype($value)) {
				case 'object':
					$value = get_class($value);
					break;
				case 'string':
					$value = '"' . $value . '"';
					break;
				case 'array':
					$value = count($value);
					break;
				case 'boolean':
					$value = $value
						? 'TRUE'
						: 'FALSE';
					break;
				case 'NULL':
					$type  = NULL;
					$value = 'NULL';
					break;
				default:
					$value = $value;
					break;
			}

			return ($type ? '[' . $type . ']' : '')           // A type if available (not on NULL)
				   . '('
				   .     $value                               // The easy representation of a value
				   .     ($detail ? (' : ' . $detail) : NULL) // Available detail
				   . ')';

		}


		/**
		 * Reflects a method and provides resolution callable
		 *
		 * @access private
		 * @return void
		 */
		private function reflectMethod()
		{
			list($class, $method) = explode('::', $this->value);
			$this->class          = ltrim($class, '\\');
			$this->method         = $method;
			$this->isMethod       = TRUE;

			try {
				$reflection = new \ReflectionMethod($class, $method);

				if (!$reflection->isPublic()) {
					$reflection->setAccessible(TRUE);
				}

				if ($reflection->isStatic()) {
					$this->call = function($args) use ($reflection) {
						return $reflection->invokeArgs(NULL, $args);
					};

				} else {
					$this->needsObject = TRUE;

					$this->call = function($object, $args) use ($reflection) {
						return $reflection->invokeArgs($object, $args);
					};
				}
			} catch (\ReflectionException $e) {
				throw new \Exception(sprintf(
					'Cannot assert undefined method "%s" on class "%s"',
					$this->method,
					$this->class
				));
			}
		}


		/**
		 * Reflects a property and provides resolution callable
		 *
		 * @access private
		 * @return void
		 */
		private function reflectProperty()
		{
			list($class, $property) = explode('::', $this->value);
			$this->class            = ltrim($class, '\\');
			$this->property         = ltrim($property, '\\$');
			$this->isProperty       = TRUE;

			try {
				$reflection = new \ReflectionProperty($this->class, $this->property);

				if (!$reflection->isPublic()) {
					$reflection->setAccessible(TRUE);
				}

				if ($reflection->isStatic()) {
					$this->call = function() use ($reflection) {
						return $reflection->getValue();
					};

				} else {
					$this->needsObject = TRUE;

					$this->call = function($object) use ($reflection) {
						return $reflection->getvalue($object);
					};
				}

			} catch (\ReflectionException $e) {
				throw new \Exception(sprintf(
					'Cannot assert undefined property "%s" on class "%s"',
					$this->property,
					$this->class
				));
			}
		}


		/**
		 * Resolves the complete assertion
		 *
		 * @access private
		 * @return mixed The assertion resolution
		 */
		private function resolve()
		{
			if ($this->isMethod || $this->isFunction || $this->isClosure) {
				$call = $this->call;

				return $this->checkObject()
					? $call($this->object, $this->args)
					: $call($this->args);

			} elseif ($this->isProperty) {
				$call = $this->call;

				return $this->checkObject()
					? $call($this->object)
					: $call();

			} else {
				return $this->value;
			}
		}
	}
}
