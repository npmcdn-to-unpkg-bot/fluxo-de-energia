@extends('master')

@section('bodyContent')
<br>
<pre>
{{ Form::open(array('url' => route("makeInvestment"))) }}

All Fields are required : 
    <label>num_shares : <input type='number' name='num_shares' value=10 /></label><br>
    <label>CHOOSE PRODUCT: <select name='product_id'>
	<option value="110">God Id 1  Fertilizer 110</option>
	<option value="108">God Id 1 Seed2 108</option>
</select>
    </label><br>
    <input type='submit' value="SAVE" />
{{ Form::close() }}
</pre>
<br>
@endsection
