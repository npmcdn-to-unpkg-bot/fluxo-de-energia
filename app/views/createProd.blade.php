
@extends('master')

@section('bodyContent')
<br>
<pre>
{{ Form::open(array('url' => route("createProduct"))) }}

All Fields are required : 

    <label>quantity : <input type='number' name='quantity' value=60 /></label><br>
    <label>quality : <input type='number' name='quantity' value=10 /></label><br>
    <label>ET: <input type='number' name='ET' value=5 /></label><br>
    <label>FT: <input type='number' name='FT' value=4 /></label><br>
    <label>unit_price: <input type='number' name='unit_price' value=10 /></label><br>
    <label>num_units: <input type='number' name='num_units' value=100 /></label><br>
    <label>name: <input type='text' name='name' value="prod" /></label><br>
    <label>TYPE: <select name='category'>
	<option value="fertilizer"> Fertilizer</option>
	<option value="seed"> Seed</option>
	<option value="land"> Land</option>
</select>
    </label><br>
<label>description: <input type='text' name='description' value="desc" /></label><br>
 
    <input type='submit' value="SAVE" />
{{ Form::close() }}

</pre>
<br>
@endsection