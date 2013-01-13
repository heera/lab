# Lab

Lab is a stupid test "framework" that works well with [Parody](http://www.github.com/dotink/parody) (and includes it by default) to provide much of the same testing capacity as more complex frameworks.

## Status

Lab is incomplete alpha software.  It's APIs are subject to change and additions without notice.  Attempts will be made to keep this document as updated as possible.

## Usage

Lab is comprised of a few different components.  For the sake of brevity we will group these components into two major pieces.

1. The `lab.php` script
2. The `library`

Although it is completely possible to use the library independently, all examples presume to be using the test execution script and, thus, may use helper functions provided within that script.

### Terminology

#### Fixture

A fixture is a collection of tests with common setup and cleanup code.  Generally speaking a fixture will test an entire class, however, for more complex classes that require significant setup or cleanup for individual pieces you might have multiple fixtures across a single class.

#### Iteration

Lab goes through a single iteration of a loop per fixture.  Each iteration is a separate execution of PHP on that fixture, so any classes which were loaded in another fixture's code will not be available unless also loaded in the current fixture's code.  If you have code which is required by all fixtures (including common constants, modifying PHP's state variables with dummy information, etc) you can execute this in the `setup` closure in the main config.

#### Test

A test is a single entry in a fixture's `tests` array.  The key is used to describe the test for result output, and the associated closure is the test code itself.  The test will fail if any uncaught exception is thrown.

### Getting Lab

```
git clone --recursive https://github.com/dotink/Lab.git lab
cd lab
```

### Configuring Lab

The `lab.config.example` script distributed with Lab provides an example configuration script.  You can copy this to `lab.config` and begin using lab right away!

```
cp lab.config.example lab.config
```

### Creating Fixtures

By default the `test_directory` value is configured one directory back.  So let's create our tests directory first:

```
mkdir ../tests
```

Let's create a simple example fixture simply called `Fixture`.  Open up a text editor and paste in the following:

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

Now save the file as `Fixture.php` in your tests directory.  Now we can run `php lab.php` in the lab directory and see our results.

![A screenshot showing basic lab output](https://dl.dropbox.com/u/31068853/lab_example_empty_fixture.png)

### A Simple Assertion

Lab includes a library which is easily accessible through a number of helper methods.  One of the classes in that library is the `Assert` class.  You can create assertions in Lab by using the `assert()` function.

By default, assertions will attempt to parse additional meaning about the values you provide.  So for example if you provide a string that looks like a callback, e.g. `MyClass::myMethod` then Lab will treat it as such.  We can see this by adding a new test to the `tests` key in our current fixture:

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

Lab allows you to test all methods on a class including private and protected methods without differentiating or performing any additional work as the required reflection is automatic.  Let's add a dummy class to our fixture's setup so that we have something to work with:

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

Note in the above code that we had to specify the namespace on the class.  Since the class was defined in our fixture which is namespaced under `Dotink\Lab` it will also reside in that namespace.  You must always use a fully qualified class name with its full namespace when referencing classes.

Now we can rerun `php lab.php`

![A screenshot showing lab running a full static method test](https://dl.dropbox.com/u/31068853/lab_example_static_method_test.png)

Using the `with()` method above, we were able to define the arguments that would be passed to the method we were testing.  For non-static methods we need to also include the `using()` method and pass in the object we want to run the method on.  Let's see that in action with one more test, add:

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
