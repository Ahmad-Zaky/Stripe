<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

        <title>Laravel</title>

    </head>
    <body class="antialiased">
        @if(Session::has('errors'))
            <script>
                $("#alertmsg").hide().slideDown();
                setTimeout(function(){
                    $("#alertmsg").slideUp();        
                }, 4000);
            </script>

            <div id="alertmsg" class="alert alert-dismissable alert-danger" style="margin: 2em;">
                <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button> 
                <strong>Error</strong>: {{ Session::get('errors')->first() }}
            </div>
        @endif
        

        <div style="display: flex; gap: 2rem; align-items:center">
            @foreach ($products as $product)
                <div>
                    <img src="{{ $product->image }}" alt="" style="max-width: 100%">
                    <h5> {{ $product->name }} </h5>
                    <p>$ {{ $product->price }}</p>
                </div>
            @endforeach
        </div>

        <div>
            <form action="{{ route("checkout") }}" method="POST">
                @csrf
                
                <button>Checkout</button>
            </form>
        </div>
    </body>
</html>
