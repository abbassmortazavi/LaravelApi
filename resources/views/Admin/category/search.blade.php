@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('css/jquery.artaraxtreeview.css') }}">
    <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>--}}
@endsection

@section('content')
    <div class="col-lg-12">
        <h2>جستجو</h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        <form action="{{ route("categories.store") }}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}


            <div id="quick-search-more" class="row gutter-5">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="parent">دسته بندی</label>
                        <select name="parent_category_id" id="parent" class="form-control">
                            <option value="0">کتاب</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->category_name }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>

                <div class="col-sm-6">
                    <div id="sub-category-container">

                    </div>
                </div>

                <input type="hidden" id="parent-category-id" name="category_id" value=0>


            </div>


            <div class="row">
                <div class="col-sm-12 end text-end">
                    <button type="submit" class="btn btn-primary pull-left">ثبت اطلاعات</button>
                </div>
            </div>
        </form>







        <br/>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title">Customer data</h2>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Category Name</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.col-lg-8 -->
@endsection


@section('script')
    <script type="text/javascript" src="{{asset('js/bootstrap_file_field.js')}}"></script>

    <script type="text/javascript" src="{{asset('js/jquery.artaraxtreeview.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.smart-file').bootstrapFileField({
                maxNumFiles: 2,
                fileTypes: 'image/jpeg,image/png',
                maxFileSize: 800000 // 80kb in bytes
            });
        });
    </script>

    <script>

        var parentCategoryId = 0
        var selectedCategoryId = 0;
        var selectCounter = 0;


        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#parent').change(function () {
                $('#sub-category-container .form-group .subCat').empty();
                $('#sub-category-container').empty();
                $('tbody').empty();
                selectedCategoryId = $("#parent option:selected").val();
                parentCategoryId = selectedCategoryId;
                if(selectedCategoryId != 0) ajaxKol(selectedCategoryId);
                $("#parent-category-id").val(selectedCategoryId);
                //console.log(parentCategoryId);
            });


        });//end document





        function ajaxKol(id) {
            $('tbody').empty();
            $.ajax({
                async:false,
                type: "POST",
                url: '{{route('loadSubCat.ajax')}}',
                data: {
                    id: id
                },
                success: function (result) {
                    if (result.length > 0)
                    {
                        $('#sub-category-container').append('<div class="form-group"><label for="exampleInputEmail1">زیر دسته</label><select id="subCat-'+selectCounter+'" onChange="selectChanged(this)" name="parent_category_id" class="form-control subCat"><option value="'+id+'"></option></select></div>');
                        $.each(result, function (index, value) {
                            $('#subCat-'+selectCounter).append('<option value=' + value['id'] + '>' + value["category_name"] + '</option>');
                        });
                        selectCounter++;
                    }
                }
            });
        }

        function selectChanged(element){
            $(element).parent().nextAll().remove();
            selectedCategoryId = $(element).val();
            var firstElementSelected = $(element).children('option:first-child').is(':selected');
            if(!firstElementSelected) {
                ajaxKol(selectedCategoryId);
            }
            $("#parent-category-id").val(selectedCategoryId);

            searchAjax(selectedCategoryId , parentCategoryId);

            //console.log(selectedCategoryId);
        }




    </script>

    <script>



        function searchAjax(id , parentCategoryId){
            $.ajax({
                async:false,
                type: "POST",
                url: '{{ route('getWord') }}',
                data: {
                    parent_category_id: id,
                    book_category_id : parentCategoryId
                },
                success: function (result) {
                    $.each(result, function (index, value) {
                        $('tbody').append('<tr><td>' + value['id'] + '</td><td>'+ value['word'] +'</td><td>' + value['persian_meaning'] + '</td> </tr>');
                    });
                }
            });
        }
    </script>


@endsection
