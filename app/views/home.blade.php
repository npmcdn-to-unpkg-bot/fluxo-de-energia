@extends('master')

@section('headContent')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>  -->

<script type="text/javascript">
setInterval(function(){
	$.ajax({
  method: "POST",
  url: "{{ route('decayHandle') }}",
  data: { 'name': "Johnny", 'location': "Boston" },
  
  success: function( le ) {
  	console.log("decay");
    $('#LE').val(parseInt(le));
  },
  
  error: function(){// ERROR NOT HANDLING ?
    alert('user not in db'); 
  }


});

  $.ajax({
  method: "POST",
  url: "{{ route('thresholdHandle') }}",
  data: { 'name': "Johnny", 'location': "Boston" },
})
  .success(function( data ) {
    console.log("sys");
    $('#sysLE').val(parseInt(data['total']));
  });


},1000);

//   $.ajax({
//   method: "POST",
//   url: "{{ route('home') }}",
//   data: { 'name': "Johnny", 'location': "Boston" },
// })
//   .success(function( data ) {
//     console.log(data);
//   });



</script>
@endsection
@section('bodyContent')
<br>

    <input type='number' id='LE' value=0 />
    <input type='number' id='sysLE' value=0 />
<!-- Returned data from server -->
<br>
@endsection
