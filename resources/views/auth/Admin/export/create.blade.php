@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('css/jquery.artaraxtreeview.css') }}">
    <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>
@endsection

@section('content')
    <div class="col-lg-12">
        <h2>گرفتن خروجی اکسل</h2>
        <div class="head-section">

        </div>
        <hr>
        @include('sweet::alert')
        <form action="{{ route("exportTable") }}" method="post">
            {{csrf_field()}}

            <div class="row gutter-5">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="parent">انتخاب جدول</label>
                        <select name="export" id="list_type" class="form-control">
                            <option value="1">دسته بندی</option>
                            <option value="2">کاربران</option>
                            <option value="3">کلمه</option>
                            <option value="4">ورد بک آپ</option>
                            <option value="5">ورد کتگوری</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row gutter-5">
                <div class="col-sm-12">
                    <div class="form-group">
                        <button class="btn btn-warning">Export</button>
                    </div>
                </div>
            </div>
        </form>


    </div>
    <!-- /.col-lg-8 -->
@endsection


@section('script')
    <script type="text/javascript" src="{{asset('js/bootstrap_file_field.js')}}"></script>

    <script type="text/javascript" src="{{asset('js/jquery.artaraxtreeview.js')}}"></script>

@endsection
