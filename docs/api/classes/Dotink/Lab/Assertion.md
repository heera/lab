# `Assertion`



## Properties


### Instance Properties
#### <span style="color:#6a6e3d;">$args</span>

Arguments held for callable assertions

#### <span style="color:#6a6e3d;">$isClass</span>

Whether not the assertion is a Class

#### <span style="color:#6a6e3d;">$isBoolean</span>

Whether not the assertion is a boolean

#### <span style="color:#6a6e3d;">$isClosure</span>

Whether not the assertion is a closure

#### <span style="color:#6a6e3d;">$isFunction</span>

Whether not the assertion is a function

#### <span style="color:#6a6e3d;">$isMethod</span>

Whether not the assertion is a method

#### <span style="color:#6a6e3d;">$isNumber</span>

Whether not the assertion is numeric (float or integer)

#### <span style="color:#6a6e3d;">$isObject</span>

Whether not the assertion is an object

#### <span style="color:#6a6e3d;">$isProperty</span>

Whether not the assertion is a property

#### <span style="color:#6a6e3d;">$isString</span>

Whether not the assertion is a string

#### <span style="color:#6a6e3d;">$needsObject</span>

Whether or not the assertion needs an object (such as for object methods/properties)

#### <span style="color:#6a6e3d;">$type</span>

The PHP determined type of the value

#### <span style="color:#6a6e3d;">$value</span>

The original value of the assertion

#### <span style="color:#6a6e3d;">$class</span>

#### <span style="color:#6a6e3d;">$method</span>

#### <span style="color:#6a6e3d;">$object</span>

#### <span style="color:#6a6e3d;">$property</span>



## Methods


### Instance Methods
<hr />

#### <span style="color:#3e6a6e;">__construct()</span>

Create a new assertion, this will determine much about the nature of our value

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
				mixed
			</td>
			<td>
				The value to assert
			</td>
		</tr>
					
		<tr>
			<td>
				$raw
			</td>
			<td>
				boolean
			</td>
			<td>
				Whether we should disable special interpretation, default FALSE
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

#### <span style="color:#3e6a6e;">contains()</span>

Asserts that one or more values is contained in the result

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
				mixed
			</td>
			<td>
				A value to check for in the result
			</td>
		</tr>
					
		<tr>
			<td>
				$...
			</td>
			<td>
				mixed
			</td>
			<td>
				ad infinitum
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The original assertion for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">begins()</span>

Asserts that the result begins with a certain value

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
				$beginning
			</td>
			<td>
				mixed
			</td>
			<td>
				A value equal to the beginning
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The original assertion for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">ends()</span>

Asserts that the result ends with a certain value

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
				$end
			</td>
			<td>
				mixed
			</td>
			<td>
				A value equal to the ending
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The original assertion for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">equals()</span>

Asserts that the result is equal to a value

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
				mixed
			</td>
			<td>
				The value to check for equality
			</td>
		</tr>
					
		<tr>
			<td>
				$exactly
			</td>
			<td>
				boolean
			</td>
			<td>
				Whether or not the comparision should be exact
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The original assertion for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">has()</span>

Asserts that the result has a given key or keys

###### Parameters

<table>
	<thead>
		<th>Name</th>
		<th>Type(s)</th>
		<th>Description</th>
	</thead>
	<tbody>
			
		<tr>
			<td rowspan="3">
				$key
			</td>
			<td>
				int
			</td>
			<td rowspan="3">
				A key to check for
			</td>
		</tr>
			
		<tr>
			<td>
				string
			</td>
		</tr>
								
		<tr>
			<td rowspan="3">
				$...
			</td>
			<td>
				int
			</td>
			<td rowspan="3">
				ad infinitum
			</td>
		</tr>
			
		<tr>
			<td>
				string
			</td>
		</tr>
						
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The original assertion for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">measures()</span>

Asserts that the length/size of the result measures to a certain number

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
				$condition
			</td>
			<td>
				string
			</td>
			<td>
				An optional condition: GT, LT, GTE, LTE
			</td>
		</tr>
					
		<tr>
			<td>
				$size
			</td>
			<td>
				int
			</td>
			<td>
				The size to compare to
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The original assertion for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">throws()</span>

Tests the current assertion to see if it throws an exception

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
				$class
			</td>
			<td>
				string
			</td>
			<td>
				The exception class to test for
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The original assertion for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">using()</span>

Provide an object to use for assertions which require an object

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
				$object
			</td>
			<td>
				object
			</td>
			<td>
				The object to use
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The assertion, for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">with()</span>

Provide arguments for assertions which are callable

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
				$arg
			</td>
			<td>
				mixed
			</td>
			<td>
				The first argument
			</td>
		</tr>
					
		<tr>
			<td>
				$...
			</td>
			<td>
				mixed
			</td>
			<td>
				ad infinitum
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			Assertion
		</dt>
		<dd>
			The assertion, for method chaining
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">checkObject()</span>

Checks whether or not an assertion requiring an object needs ones.

###### Returns

<dl>
	
		<dt>
			boolean
		</dt>
		<dd>
			TRUE if an object is needed and available, FALSE otherwise
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">loadArray()</span>

All the requisite logic for loading an array assertion

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

#### <span style="color:#3e6a6e;">loadBoolean()</span>

All the requisite logic for loading a boolean assertion

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

#### <span style="color:#3e6a6e;">loadNumber()</span>

All the requisite logic for loading a numeric assertion

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

#### <span style="color:#3e6a6e;">loadString()</span>

All the requisite logic for loading a string assertion

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
				$raw
			</td>
			<td>
				boolean
			</td>
			<td>
				Whether or not we should try special interpretations
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

#### <span style="color:#3e6a6e;">loadObject()</span>

All the requisite logic for loading an object assertion

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

#### <span style="color:#3e6a6e;">formatValue()</span>

Formats a value somewhat neatly (depending on type) into a printable string

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
				mixed
			</td>
			<td>
				The value to format
			</td>
		</tr>
			
	</tbody>
</table>

###### Returns

<dl>
	
		<dt>
			string
		</dt>
		<dd>
			A hopefully nice string represenation of the original value
		</dd>
	
</dl>

<hr />

#### <span style="color:#3e6a6e;">reflectMethod()</span>

Reflects a method and provides resolution callable

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

#### <span style="color:#3e6a6e;">reflectProperty()</span>

Reflects a property and provides resolution callable

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

#### <span style="color:#3e6a6e;">resolve()</span>

Resolves the complete assertion

###### Returns

<dl>
	
		<dt>
			mixed
		</dt>
		<dd>
			The assertion resolution
		</dd>
	
</dl>



