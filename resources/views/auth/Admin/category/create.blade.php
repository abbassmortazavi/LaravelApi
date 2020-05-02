@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('css/jquery.artaraxtreeview.css') }}">
    <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>--}}
@endsection

@section('content')
    <div class="col-lg-12">
        <h2>ثبت دسته </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        <form action="{{ route("categories.store") }}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}


            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>رایگان</label>
                            <input type="checkbox" value="1" name="is_free">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>published</label>
                                    <input type="checkbox" value="1" name="is_published">
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-sm-6">
                            <div class="form-group">
                                <label for="parent">دسته بندی</label>
                                <select name="parent_category_id11" id="parent1111" class="form-control">
                                    <option value="0">کتاب</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>-->
                    </div>

                </div>
            </div>


            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="cat_name">نام دسته</label>
                      <!--  <input type="text" id="category_name" class="form-control" name="category_name"
                               placeholder="نام دسته، ...">-->

<input type="text" id="category_name" class="form-control" name="category_name"
                               placeholder="نام دسته، ..." value="{{ old('category_name') }}">

                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="parent"> نوع زیر دسته</label>
                        <select name="list_type" id="list_type" class="form-control">
                            <option value="1">کتاب</option>
                            <option value="2">سر فصل</option>
                            <option value="3">کلمه</option>
                        </select>
                    </div>
                </div>
            </div>

           <div class="col-sm-12">

                <div class="form-group">
                    <label for="description">توضیحات فارسی:</label>
                    <textarea class="form-control" rows="5" name="description" id="description"></textarea>
                </div>
            </div>

            <!--<div class="col-sm-12">

                <div class="form-group">
                    <label for="descriptionEn">توضیحات لاتین:</label>
                    <textarea class="form-control" rows="5" name="descriptionEn" id="descriptionEn"></textarea>
                </div>
            </div>-->

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

                <input type="hidden" id="parent-category-id" name="parent_category_id" value=0>



                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-lg-12 animatedParent animateOnce z-index-50">
                            <div class="panel panel-default animated fadeInUp">
                                <div class="panel-heading clearfix">
                                    <h3 class="panel-title">انتخاب تصاویر</h3>
                                </div>
                                <div  class="panel-body">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label style="display: grid;">تصاویر را انتخاب کنید :    &nbsp;
                                                <em>همه تصاویر را بصورت هم زمان انتخاب کنید</em>
                                            </label>
                                            <input type="file" class="" name="image_path[]"
                                                   data-field-type="bootstrap-file-filed"
                                                   data-label="انتخاب گالری تصاویر"
                                                   data-btn-class="btn-primary"
                                                   data-file-types="image/jpeg,image/png,image/gif"
                                                   data-preview="on"
                                                   multiple
                                                   style="display:block;width: 200px;"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>


            <div class="row">
                <div class="col-sm-12 end text-end">
                    <button type="submit" class="btn btn-primary pull-left">ثبت اطلاعات</button>
                </div>
            </div>
        </form>
            
            <div class="search">
                 <form class="m-form m-form--fit m--margin-bottom-20" id="dataTableSearchForm">
                <div class="row">
                    <div class="col-sm-6">
                            <div class="form-group">
                                <label for="parent">جستجو براساس دسته بندی </label>
                                <select name="parent_category_id" id="parent1" class="form-control">
                                    <option value="0">کتاب</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    <div class="col-sm-6">
                            <div id="sub1-category-container">
    
                            </div>
                        </div>
                </div>
                <input type="hidden" id="parent1-category-id" name="parent_category_id" value=0>
                    
            </form>
            </div>
           
       

        

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
                            <th>parent_category_id</th>
                            <th>Image</th>
                            <th>Free</th>
                            <th>List Type</th>
                            <th>Published</th>
                            <th>Delete</th>
                            <th>Edit</th>
                        </tr>
                        </thead>
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
        
        var parentCategoryId1 = 0
        var selectedCategoryId1 = 0;
        var selectCounter1 = 0;
        
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#parent').change(function () {
                $('#sub-category-container .form-group .subCat').empty();
                $('#sub-category-container').empty();
                selectedCategoryId = $("#parent option:selected").val();
                if(selectedCategoryId != 0) ajaxKol(selectedCategoryId);
                $("#parent-category-id").val(selectedCategoryId);
                //console.log(selectedCategoryId);
            });

            
            $('#parent1').change(function () {
                $('#sub1-category-container .form-group .subCat').empty();
                $('#sub1-category-container').empty();
                selectedCategoryId1 = $("#parent1 option:selected").val();
                if(selectedCategoryId1 != 0) ajaxKol1(selectedCategoryId1);
                $("#parent1-category-id").val(selectedCategoryId1);
                //console.log(selectedCategoryId1);
                searchAjax(selectedCategoryId1);
                
            });

        });//end document

        
        function ajaxKol1(id) {
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
                        $('#sub1-category-container').append('<div class="form-group"><label for="exampleInputEmail1">زیر دسته</label><select id="subCat1-'+selectCounter1+'" onChange="selectChanged1(this)" name="parent_category_id" class="form-control subCat"><option value="'+id+'"></option></select></div>');
                        $.each(result, function (index, value) {
                            $('#subCat1-'+selectCounter1).append('<option value=' + value['id'] + '>' + value["category_name"] + '</option>');
                        });
                        selectCounter1++;
                    }
                }
            });
        }
        

        function ajaxKol(id) {
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
            console.log(selectedCategoryId);
        }

        function selectChanged1(element){
            $(element).parent().nextAll().remove();
            selectedCategoryId1 = $(element).val();
            var firstElementSelected1 = $(element).children('option:first-child').is(':selected');
            if(!firstElementSelected1) {
                ajaxKol1(selectedCategoryId1);
            }
            $("#parent1-category-id").val(selectedCategoryId1);
             searchAjax(selectedCategoryId1);
            //console.log(selectedCategoryId1);
        }
        
        
    </script>

    <script>
        let dataTableMe;
        /*$(function () {
             dataTableMe = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('category') }}',
                data:
                    function (d) {
                        d._token = $('meta[name="csrf-token"]').attr('content');
                        d.parent_category_id = $('#parent1-category-id').val();
                    },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'description', name: 'description'},
                    {data: 'parent_category_id', name: 'parent_category_id'},
                    {data: 'image_path', name: 'image_path'},
                    {data: 'is_free', name: 'is_free'},
                    {data: 'list_type', name: 'list_type'},
                    {data: 'is_published', name: 'is_published'},
                    {data: 'delete', name: 'delete', orderable: false, searchable: false},
                    {data: 'edit', name: 'edit', orderable: false, searchable: false},
                ]
            });

        });*/
         
        $(function () {

            dataTableMe = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url('category') }}',
                    type: "POST",
                    //dataType: "json",
                    //delay: 2000,
                    data:
                        function (d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.parent_category_id = $('#parent1-category-id').val();
                        }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'description', name: 'description'},
                    {data: 'parent_category_id', name: 'parent_category_id'},
                    {data: 'image_path', name: 'image_path'},
                    {data: 'is_free', name: 'is_free'},
                    {data: 'list_type', name: 'list_type'},
                    {data: 'is_published', name: 'is_published'},
                    {data: 'delete', name: 'delete', orderable: false, searchable: false},
                    {data: 'edit', name: 'edit', orderable: false, searchable: false},
                ],
                order: [[1, 'asc']]

            });


            $('#dataTableSearchForm').on('submit', function(e) {
                dataTableMe.draw();
                e.preventDefault();
            });

        });





        $(document).on("click", "#delete",function () {
            let id = $(this).attr('data-id');
            swal({
                title: "آیا مطمئن به حذف هستید؟",
                text: "بعد از حذف این دسته بندی دیگه هیچ وقت بهش دسترسی ندارید!!",
                icon: "error",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {

                        $.ajax({
                            async:false,
                            type: "POST",
                            url: '{{route('deleteCat.ajax')}}',
                            data: {
                                id: id
                            },
                            success: function (result) {
                                console.log(result);
                                swal("دسته مورد نظر شما با موفقیت حذف شده است!!", {
                                    icon: "success",
                                });
                                //window.location.reload();
                                dataTableMe.draw();
                            }
                        });



                    } else {
                        swal("از حذف منصرف شده اید!!");
                    }
                });

        });

        function searchAjax(id){
              $.ajax({
                async:false,
                type: "POST",
                url: '{{url('category')}}',
                data: {
                    parent_category_id: id
                },
                success: function (result) {
                    //console.log(result);
                    dataTableMe.draw();
                }
            });
        }
    </script>

    <script>
       

    </script>

@endsection
