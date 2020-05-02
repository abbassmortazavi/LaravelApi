@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@endsection

@section('content')
    <div class="col-lg-12">
        <h2>ویرایش دسته </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        <form action="{{ route("categories.update" , $category->id) }}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            {{ method_field('PATCH') }}

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="is_free">رایگان</label>
                        <select name="is_free" id="is_free" class="form-control">
                            <option value="0">Cash</option>
                            <option value="1" {{ $category->is_free == 1 ? 'selected': "" }}>Free</option>
                        </select>

                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="is_published">انتشار</label>
                        <select name="is_published" id="is_published" class="form-control">
                            <option value="0">منتشر نشود</option>
                            <option value="1" {{ $category->is_published == 1 ? 'selected': "" }}>منتشر شود</option>
                        </select>

                    </div>
                </div>
            </div>


            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="cat_name">نام دسته</label>
                        <input type="text" id="category_name" class="form-control" name="category_name"
                               placeholder="نام دسته، ..." value="{{ $category->category_name }}">

                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="parent"> نوع زیر دسته</label>
                        <select name="list_type" id="list_type" class="form-control">
                            <option value="1" {{ $category->list_type == 1 ? 'selected' : '' }}>کتاب</option>
                            <option value="2" {{ $category->list_type == 2 ? 'selected' : '' }}>سر فصل</option>
                            <option value="3" {{ $category->list_type == 3 ? 'selected' : '' }}>کلمه</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">

                <div class="form-group">
                    <label for="description">توضیحات:</label>
                    <textarea class="form-control" rows="5" name="description" id="description">{{ $category->description }}</textarea>
                </div>
            </div>

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
                        <div class="col-lg-6 animatedParent animateOnce z-index-50">
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="description">تصویر</label>
                                <img width="100" height="100" class="img-fluid img-rounded" src="{{ asset('packageImages/'.$category->image_path) }}">
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

    </div>
    <!-- /.col-lg-8 -->
@endsection


@section('script')
    <script type="text/javascript" src="{{asset('js/bootstrap_file_field.js')}}"></script>
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
                selectedCategoryId = $("#parent option:selected").val();
                if(selectedCategoryId != 0) ajaxKol(selectedCategoryId);
                $("#parent-category-id").val(selectedCategoryId);
                console.log(selectedCategoryId);
            });

        });//end document


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

    </script>





@endsection
