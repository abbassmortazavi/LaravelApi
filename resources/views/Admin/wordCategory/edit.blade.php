@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>--}}
    <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>
@endsection
{{--action="{{ route("wordCategories.update" , $wordCategory->id) }}" method="post"--}}
@section('content')
    <div class="col-lg-12">
        <h2>ویرایش کلمه به دسته بندی </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        <form>


            <div id="quick-search-more" class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="word_id">انتخاب کلمه</label>
                        <select name="word_id" id="word_id" class="form-control" multiple="multiple">
                            <option value="0">کلمه</option>
                            @foreach($words as $word)
                                <option value="{{ $word->id }}" {{ $wordCategory->word_id == $word->id ? "selected" : ""}}>{{ $word->word }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>


            <div id="quick-search-more" class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="parent">دسته بندی</label>
                        <select name="book_category_id" id="parent" class="form-control">
                            <option value="0">کتاب</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ $wordCategory->category_id == $parent->id ? "selected" : ""}}>{{ $parent->category_name }}</option>
                            @endforeach
                        </select>
                    </div>


                </div>

                <div class="col-sm-6">
                    <div id="sub-category-container">

                    </div>
                </div>

                <input type="hidden" id="parent_category_id" name="category_id" value=0>


            </div>

            <div class="row">
                <div class="col-sm-12 end text-end">
                    {{--<button type="submit" class="btn btn-primary pull-left">ثبت اطلاعات</button>--}}
                    <a id="send" class="btn btn-primary pull-left">ویرایش اطلاعات</a>
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
        var bookId = 0;
        var parentCategoryId = 0;
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
                $("#parent_category_id").val(selectedCategoryId);
                //console.log(selectedCategoryId);
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
                        $('#sub-category-container').append('<div class="form-group"><label for="exampleInputEmail1">زیر دسته</label><select id="subCat-'+selectCounter+'" onChange="selectChanged(this)" name="category_id" class="form-control subCat"><option value="'+id+'"></option></select></div>');
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
            $("#parent_category_id").val(selectedCategoryId);
            bookId = $("#sub-category-container").find(">:first-child").find("select").val();
            //console.log(selectedCategoryId);
        }

    </script>

    <script>
        let word_ids = 0;
        $(document).ready(function () {
            /*word_id = $("#word_id option:selected").val();
            if (word_id == 0)
            {
                word_id = $("#word_id option:selected").val();
            }
            $('#word_id').change(function () {
                word_id = $("#word_id option:selected").val();
            });*/
            
             $('#word_id').change(function () {
                word_ids = $('#word_id').val();

            });

            $('#parent').change(function () {
                parentCategoryId = $("#parent option:selected").val();
            });

            
            $('#send').click(function () {
                $.ajax({
                    async: false,
                    type: "POST",
                    url: '{{route('updateWordCat.ajax' , $wordCategory->id)}}',
                    data: {
                        word_ids: word_ids,
                        category_id: selectedCategoryId,
                        book_category_id: bookId,
                    },
                    success: function (result) {
                        console.log(result);
                        if (result == "1"){
                           swal("کلمه یا دسته بندی مورد نظرتان را انتخاب نکرده اید برای ثبت کلمه به دسته بندی حتما باید هر دو را انتخاب کنید!با تشکر", {
                               icon: "warning",
                           });
                       }
                        if (result == "2"){
                            swal("این کلمه در این دسته بندی انتخابی شما موجود می باشد!!", {
                                icon: "warning",
                            });
                        }
                        
                        if (result == "3"){
                            swal("حتما باید یکی از تعداد فرزندان دسته بندی ها را انتخاب کنید!!", {
                                icon: "warning",
                            });
                        }
                        $('#word_id').val(null).trigger('change');
                    
                    }
                });
            });
            
            
            
            
        });
    </script>
    <script>
       $(document).ready(function() {
           /* $('.word_id').select2({
                search:true,
                sortable:false
            });*/
            $('.word_id').select2();
            $('select').on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);

                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
            });



        });
    </script>

@endsection
