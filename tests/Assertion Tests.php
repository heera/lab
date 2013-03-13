<?php namespace Dotink\Lab
{
	return [
		'tests' => [

			//
			//
			//

			'Simple Assertions' => function($data) {
				assert(1+1)->equals(2);
				assert(NULL)->equals(FALSE);

				assert('12345')->measures(5);
				assert('12345')->measures(GT, 4);
				assert('12345')->measures(LT, 6);
			},

			//
			//
			//

			'Assertions on Closures' => function($data) {
				assert(function(){ return 1; })->equals(1, TRUE);
				assert(function(){ return 'test'; })->measures(4);
			},

			//
			//
			//

			'Negated Assertions' => function($data) {

				assert(function(){ assert(1+1)->equals(3); })->throws('Exception');
				assert(function(){ assert(NULL)->equals(FALSE, TRUE); })->throws('Exception');

			}

		]
	];
}