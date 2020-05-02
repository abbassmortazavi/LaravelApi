<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{csrf_token()}}"/>
    <title>Laravel</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="container">
    <h3>import upload excel file</h3>
    <br/>
    @if(count($errors) > 0)

        <div class="alert alert-danger">
            Upload Validation Error<br><br>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    @if($message = \Illuminate\Support\Facades\Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">*</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif
    {{--<form action="{{ url('/import_excel/importWordCat') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-group">
            <table class="table">
                <tr>
                    <td width="40%" align="right">
                        <label>select file for upload...</label>
                    </td>
                    <td width="30%">
                        <input type="file" name="select_file">
                    </td>
                    <td width="30%" align="left">
                        <input type="submit" name="upload" class="btn btn-primary" value="Upload">
                    </td>
                </tr>
                <tr>
                    <td width="40%" align="right"></td>
                    <td width="30%"><span class="text-muted">.xls, .xslx</span></td>
                    <td width="30%" align="left"></td>
                </tr>
            </table>
        </div>
    </form>--}}


    <hr>
    <form method="get" action="{{ url('pay') }}">
        {{ csrf_field() }}
        <input id="tokenField" type="text" value="" name="token">
        <input id="tokenField" type="text" value="" name="package_id">
        <input type="submit">
    </form>


   
</div>





</body>
</html>
