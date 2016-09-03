
@extends('master')

@section('bodyContent')
<br>
<script type="text/javascript">
    function getUC(){
        var ET=document.getElementById('ET').value;
        var FT=document.getElementById('FT').value;
        var Tol=document.getElementById('Tol').value;
        var quality=document.getElementById('quality').value;
        var type=document.getElementById('category').value;
        <?php 
        $c1=Config::get('game.baseC1');
        $c2=Config::get('game.baseC2');
        $c3=Config::get('game.baseC3');
        $c4=Config::get('game.baseC4');
        $k=Config::get('game.basePrice');
        ?>
        var c1={{ $c1 }};
        var c2={{ $c2 }};
        var c3={{ $c3 }};
        var c4={{ $c4 }};
        var basePrice = {"seed": 500,"fertilizer": 2000,"land": 7000},
        var bp=basePrice[type];
        return bp* (c1*quality+c2*FT+c3*ET)*(1+c4*Tol);
    }


//check syntax & ids here-
$('.MySliders').change(

    $('#unitPrice').val(getUC());

    );


</script>
<pre>
    {{ Form::open(array('url' => route("createProduct"))) }}

    All Fields are required : 

    <label>quality : <input type='number' name='quality' value=10 /></label><br>
    <label>ET: <input type='number' name='ET' value=5 /></label><br>
    <label>FT: <input type='number' name='FT' value=4 /></label><br>
    <label>unit_price: <input type='number' name='unit_price' value="2000" disabled="true" /></label><br>
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
