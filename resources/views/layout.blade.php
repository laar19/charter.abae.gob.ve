<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
        
        <link href="{{ asset('/assets/css/main.css') }}" rel="stylesheet">
        
        <!-- Bootstrap Core CSS -->
        <link href="{{ asset('/assets/css/bootstrap.min.css') }}" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    </head>

    <body>
        <header>
            <center><img src="{{ asset('/assets/img/cintillo-julio-2017.png') }}"></center>
        </header>
        <br><br><br>

        @yield('content')

        <!-- jQuery -->
        <script src="{{ asset('/assets/js/jquery/jquery-3.2.1.min.js') }}"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="{{ asset('/assets/js/bootstrap.min.js') }}"></script>
    </body>
</html>
