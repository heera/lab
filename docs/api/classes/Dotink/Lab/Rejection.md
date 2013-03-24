# Rejection
## A simple rejection library

_Copyright (c) 2013, Matthew J. Sahagian_.
_Please reference the LICENSE.md file at the root of this distribution_

### Details

This class is essentially a proxy class for Assertion.  That is to say, you should expect
that any publically accessible methods available on Assertion are available on this class
and follow the same API.  The *only* difference between Assertion and this class is that
Assertion makes positive claims and this class makes negative claims.

#### Namespace

`Dotink\Lab`

#### Imports

<table>

	<tr>
		<th>Alias</th>
		<th>Namespace / Class</th>
	</tr>
	
	<tr>
		<td>Exception</td>
		<td>Exception</td>
	</tr>
	
	<tr>
		<td>InvalidArgumentException</td>
		<td>InvalidArgumentException</td>
	</tr>
	
</table>

#### Authors

<table>
	<thead>
		<th>Name</th>
		<th>Handle</th>
		<th>Email</th>
	</thead>
	<tbody>
	
		<tr>
			<td>
				Matthew J. Sahagian
			</td>
			<td>
				mjs
			</td>
			<td>
				msahagian@dotink.org
			</td>
		</tr>
	
	</tbody>
</table>

## Properties

### Instance Properties
#### <span style="color:#6a6e3d;">$assertion</span>




## Methods

### Instance Methods
<hr />

#### <span style="color:#3e6a6e;">__construct()</span>

Create a new rejection

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td>
				$value
			</td>
			<td>
									<a href="http://www.php.net/language.pseudo-types.php">mixed</a>
				
			</td>
			<td>
				The subject of our assertion
			</td>
		</tr>
					
		<tr>
			<td>
				$raw
			</td>
			<td>
									<a href="http://www.php.net/language.types.boolean.php">boolean</a>
				
			</td>
			<td>
				Whether we should attempt anything smart on $value, default FALSE
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			void
		</dt>
		<dd>
			Provides no return value.
		</dd>
	
</dl>


<hr />

#### <span style="color:#3e6a6e;">__call()</span>

Proxies methods to our internal assertion

##### Details

If the method called is actually one of the assertion methods, such as `equals()` or
`has()`, for example, then this method will wrap the assertion and ensure it throws
an exception, thereby asserting the opposite, or, rejecting.

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td>
				$method
			</td>
			<td>
									<a href="http://www.php.net/language.types.string.php">string</a>
				
			</td>
			<td>
				The method called
			</td>
		</tr>
					
		<tr>
			<td>
				$args
			</td>
			<td>
									<a href="http://www.php.net/language.types.array.php">array</a>
				
			</td>
			<td>
				The arguments passed
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Rejection
		</dt>
		<dd>
			The Rejeciton object for method chaining
		</dd>
	
</dl>






