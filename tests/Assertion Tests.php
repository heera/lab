<?php namespace Dotink\Lab
{
	return [

		//
		// Out setup adds a stupid class called Calculator, pretty useless in real life, but
		// not so bad for testing.
		//

		'setup' => function($data) {

			//
			// A stupid calculator class
			//

			class Calculator
			{
				private $seed  = NULL;
				private $value = NULL;

				public function __construct($seed)
				{
					$this->seed = $seed;
				}

				public function add($subject)
				{
					$this->value = $this->seed + $subject;

					return $this->equals();
				}

				private function equals()
				{
					return $this->value;
				}

			}
		},

		'tests' => [

			//
			// Simple assertions test most common methods on simple input
			//

			'Simple Assertions' => function($data) {
				assert(1+1)->equals(2);
				assert(NULL)->equals(FALSE);

				assert('12345')->measures(5);
				assert('12345')->measures(GT, 4);
				assert('12345')->measures(LT, 6);

				assert('abcd')->measures(GTE, 3);
				assert('abcd')->measures(LTE, 5);
			},

			//
			//  Assertions on closures use the return value of the closure to test against
			//

			'Assertions on Closures' => function($data) {
				assert(function(){ return 1; })->equals(1, TRUE);
				assert(function(){ return 'test'; })->measures(4);
			},

			//
			// Negated assertions make sure our simple assertions will throw exceptions if they're
			// failed tests.
			//

			'Negated Assertions' => function($data) {

				assert(function(){ assert(1+1)->equals(3);            })->throws('Exception');
				assert(function(){ assert(NULL)->equals(FALSE, TRUE); })->throws('Exception');

				assert(function(){ assert('12345')->measures(6);      })->throws('Exception');
				assert(function(){ assert('12345')->measures(GT, 5);  })->throws('Exception');
				assert(function(){ assert('12345')->measures(LT, 5);  })->throws('Exception');

				assert(function(){ assert('abcd')->measures(GTE, 5);  })->throws('Exception');
				assert(function(){ assert('abcd')->measures(LTE, 3);  })->throws('Exception');
			},

			//
			// Tests "smart" (parsed) assertions using a specific object.  This tests both
			// private variable access, private method access, and public method access using
			// with() to pass arguments.
			//

			'Smart Assertions' => function($data) {
				$calculator1 = new Calculator(5);
				$calculator2 = new Calculator(10);
				$calculator3 = new Calculator(-7);

				//
				// Checks a private variable
				//

				assert('Dotink\Lab\Calculator::$seed')
					-> using($calculator1) -> equals(5)
					-> using($calculator2) -> equals(10)
					-> using($calculator3) -> equals(-7)
				;

				//
				// Runs a public method
				//

				assert('Dotink\Lab\Calculator::add')
					-> using($calculator1) -> with(5) -> equals(10)
					-> using($calculator2) -> with(3) -> equals(13)
					-> using($calculator3) -> with(2) -> equals(-5)
				;

				//
				// Access a private method
				//

				assert('Dotink\Lab\Calculator::equals')
					-> using($calculator1) -> equals(10)
					-> using($calculator2) -> equals(13)
					-> using($calculator3) -> equals(-5)
				;


			}
		]
	];
}