<!doctype html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{csrf_token()}}" />
    <title>Laravel</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>

<style>
    body {
    background: #f5f5f5;
    font-family: 'Noto Sans', sans-serif;
    margin: 0;
    color: #4c5667;
    overflow-x: hidden !important;
    padding-top:40px;
}

.message-box h1 {
    color: #252932;
    font-size: 98px;
    font-weight: 700;
    line-height: 98px;
    text-shadow: rgba(61, 61, 61, 0.3) 1px 1px, rgba(61, 61, 61, 0.2) 2px 2px, rgba(61, 61, 61, 0.3) 3px 3px;
}
</style>

<div class="container">
   
    <div class="row">

    </div>
    <div class="ex-page-content bootstrap snippets">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                @if($data['state'] == "error")
                    <div class="alert text-center text-danger">
                        <h4>{{ $data['message'] }}</h4>
                    </div>
                @endif
                @if($data['state'] == "success")
                    <div class="success text-center text-success">
                        <h4>{{ $data['message'] }}</h4>
                    </div>
                @endif
            </div>
            <div class="col-sm-12 text-center" style="margin-top:20px;" >
                    <img class="img-responsive" src="about/images/logo.png">
            </div>

            <div class="col-sm-12">
                <div class="message-box">
                    <div class="buttons-con text-center" style="margin-top:40px;">
                        @if($data['state'] == "error")
                            <div class="action-link-wrap">
                                <a href="{{ url('/paymentErrorPage') }}" class="btn btn-custom btn-info waves-effect waves-light m-t-20" style="width:120px;">Proceed</a>
                            </div>
                        @endif
                        @if($data['state'] == "success")
                            <div class="action-link-wrap">
                                <a href="{{ url('/paymentSuccessPage') }}" class="btn btn-custom btn-info waves-effect waves-light m-t-20" style="width:120px;">Proceed</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</div>





</body>
</html>
