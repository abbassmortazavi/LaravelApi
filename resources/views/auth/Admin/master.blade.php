<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Topleit</title>


  
    <link href="{{ asset("css/jquery.dataTables.min.css") }}" rel="stylesheet" type="text/css">


    <link href="{{ asset("css/font-awesome.min.css") }}" rel="stylesheet" type="text/css">
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset("css/bootstrap.min.css") }}" rel="stylesheet">
    <!--<link href="{{ asset("css/bootstrap-select.min.css") }}" rel="stylesheet">-->
        <link href="{{ asset("css/select2.min.css") }}" rel="stylesheet" />
        
        <link href="{{ asset("css/jquery-ui.min.css") }}" rel="stylesheet" />
        
        <link href="{{ asset("css/multi-select.css") }}" rel="stylesheet" />

    <!-- not use this in ltr -->
    <link href="{{ asset("css/bootstrap1.rtl.css") }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset("css/sb-admin-2.css") }}" rel="stylesheet">
    <link href="{{ asset("css/fontIran.css") }}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

    <!-- Custom Fonts -->

    @yield('style')
</head>

<body>


    @include('Admin.section.header')
           @yield("content")
    @include("Admin.section.footer")



</body>

</html>
