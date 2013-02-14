# Lab

Lab is a stupid test "framework" that works well with [Parody](http://www.github.com/dotink/parody) (and includes it by default) to provide much of the same testing capacity as more complex frameworks.

## Status

Lab is beta software.  It's APIs are subject to change and additions without notice.  Attempts will be made to keep this document as updated as possible.

## Usage

Lab is comprised of two major components.

1. The `lab.php` script
2. The `Assertion` class

Although it is completely possible to use the `Assertion` class independently, all examples presume to be using the `lab.php` test execution script and, thus, may use helper functions provided within that script.

### Terminology

#### Fixture

A fixture is a "fixed" or known state which the environment is put into before performing tests.  Like most frameworks, Lab provides points for setting up a fixture and "tearing it down," although Lab calls this the "cleanup" phase.  Lab is organized in such a way that one test file should represent one fixture with multiple tests that need to use that fixture.

#### Cycle

A single cycle consists of a global and local setup for the fixture, a series of tests, and finally a local and global cleanup for that fixture; in that order.  Each cycle, additionally, is run in an isolated context.  This is achieved by independent executions of PHP for each test file in the tests directory.

#### Test

A single test is a single function in the `tests` array of any file in our tests directory.  A single test may contain multiple assertions which upon failure will throw an exception.

### Getting Lab

The easiest way to get lab is to clone it using git.

```
git clone --recursive https://github.com/dotink/Lab.git lab
cd lab
```

### Configuring Lab

The `lab.config` script distributed with Lab provides an example usable configuration script.  You can configure a project's tests by copying and modifying this to the project directory.

### Setting Up a Fixture and adding Tests

The `lab.config` file contians a `tests_directory` key in its configuration array.  By default, or if a configuration is not available, the value of this will simply be `tests`.  Each file in this directory should represent a single fixture and a series of tests related to that fixture.

Let's create a simple example test file and just call it `Fixture`.  Open up a text editor and paste in the following:

```php
<?php namespace Dotink\Lab {

	use Dotink\Parody;

	return [

		'setup' => function() {

		},

		'cleanup' => function() {

		},

		'tests' => [
			'Example test which does nothing' => function() {

			}
		]
	];
}
```

Now save the file as `Fixture.php` in your tests directory.  It is important to note that because we have not configured any global or local setup logic, this is essentially an "empty" fixture.  Now we can run `php lab.php` in the lab directory and see our results.

![A screenshot showing basic lab output](https://dl.dropbox.com/u/31068853/lab_example_empty_fixture.png)

### A Simple Assertion

Lab includes a library which is easily accessible through a few shorthand helper functions.  One of the classes in that library is the `Assertion` class.  You can create assertions in Lab by using the `assert()` function.

By default, assertions will attempt to parse additional meaning about the values you provide.  So for example if you provide a string that looks like a method call, e.g. `MyClass::myMethod` then Lab will treat it as such.  We can see this by adding a new test to the `tests` key for our existing empty fixture:

```php
'tests' => [
	'Example test which does nothing' => function() {

	},

	'A test to show how Lab parses assertions' => function() {
		assert('MyClass::myMethod');
	},
]
```

Now rerun `php lab.php`

![A screenshot showing how Lab parses assertions](https://dl.dropbox.com/u/31068853/lab_example_parsing_assertion.png)

If we want to test raw values, we can pass `TRUE` as a second parameter to assert.  Let's change the above added test to run the following instead:

```php
assert('MyClass::myMethod', TRUE)
	-> equals('MyClass::myMethod');
```

And rerun `php lab.php`:

![A screenshot showing lab running with raw value assertion](https://dl.dropbox.com/u/31068853/lab_example_asserting_raw_values.png)

### Testing Full Methods

Lab's assertion class allows you to test all methods including private and protected.  Testing these methods is completely automatic and there is no additional setup required.  To get an idea of how assertions work, let's add a dummy class to our local setup for the fixture. so that we have something to work with:

```php
'setup' => function() {
	class Foo
	{
		static public function lower($value)
		{
			return strtolower($value);
		}

		private function bailOnEmpty($value)
		{
			if (empty($value)) {
				throw new \Exception('The value is empty');
			} else {
				return $value;
			}
		}
	}
},
```

And we'll add the following test to our tests array:

```php
'lower()' => function() {
	assert('Dotink\Lab\Foo::lower')
		-> with('Lab is my FAVORITE!')
		-> equals('lab is my favorite!');
},
```

Note that because we defined the class inside this file it's namespace is actually `Dotink\Lab`, so we need to make sure we specify that in the call to assert.  The assert method always assumes to be working out of the global namespace, so you must specify the complete namespace minus the root (leading) `\` at the very beginning.

Now we can rerun `php lab.php`

![A screenshot showing lab running a full static method test](https://dl.dropbox.com/u/31068853/lab_example_static_method_test.png)

Using the `with()` method shown above, we are able to define the arguments that would be passed to the method we were testing.  If the method is not static, we can provide the object to execute on with the `using()` method:

```php
'bailOnEmpty()' => function() {
	$foo = new Foo();

	assert('Dotink\Lab\Foo::bailOnEmpty')
		-> using($foo)
		-> with('')
		-> throws('Exception');

	assert('Dotink\Lab\Foo::bailOnEmpty')
		-> using($foo)
		-> with('non-empty value')
		-> equals('non-empty value');
}
```

And now rerun `php lab.php`:

![A screenshot showing lab running a method test on a private, non-static method which might throw an exception](https://dl.dropbox.com/u/31068853/lab_example_using_and_throws.png)

The previous example shows all of the following:

- Lab testing a private method
- Lab testing a non-static method
- Lab testing whether the method throws a certain exception with the `throws()` method

### Dealing with Dependencies

Although it is also a separate library, Lab uses .inK's [Parody](http://www.github.com/dotink/parody) to deal with mocking/stubbing dependencies.  We hope you find both Parody and Lab a joy to use.

## Conclusion

We here at .inK hope that you have fun using Lab and if you're interested in contributing we appreciate it as it still has a long way to go.
