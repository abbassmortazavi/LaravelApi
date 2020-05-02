@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@endsection

@section('content')
    <div class="col-lg-12">
        <h2>ویرایش لیسنینگ  کتگوری </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')

        <form action="{{ route("listeningCategories.update" , $listeningCategory->id) }}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            @method('Patch')
            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="title">عنوان</label>
                        <input type="text" name="title" class="form-control" value="{{ $listeningCategory->title }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>رایگان</label>
                            <input type="checkbox" name="is_free"  {{ $listeningCategory->is_free == 1 ? "checked":"" }}>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>published</label>
                                    <input type="checkbox" name="is_published" {{ $listeningCategory->is_published == 1 ? "checked":"" }}>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="title">سطح</label>
                        <select class="form-control" name="level">
                            <option value="1" {{ $listeningCategory->level == 1 ? "selected":"" }}>پایه</option>
                            <option value="2" {{ $listeningCategory->level == 2 ? "selected":"" }}>متوسط</option>
                            <option value="3" {{ $listeningCategory->level == 3 ? "selected":"" }}>پیشرفته</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for="description">توضیحات:</label>
                    <textarea class="form-control" rows="5" name="description" id="description">
                        {{$listeningCategory->description}}
                    </textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="custom-file">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                        <input type="file" class="custom-file-input" name="image_path" id="customFile">
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
