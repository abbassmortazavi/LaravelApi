@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/4.15.0/lodash.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>--}}
@endsection
{{--action="{{ route("wordCategories.store") }}" method="post"--}}
@section('content')
    <div class="col-lg-12">
        <h2>ثبت کلمه به دسته بندی </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        
    </div>
    
    <script>
        var wordsArray = [];
    </script>
    
    @foreach($words as $word)
    <?php $wordText = $word->word;
    $wordText = preg_replace("/[\n\r]/","",$wordText);
    ?>
        <script>
            var word = {
                id: <?php echo $word->id;?>,
                text : "<?php echo $wordText;?>"
            }
            wordsArray.push(word);
        </script>
    @endforeach


        <form id="formSendData">
            
            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="category_type">نوع دسته بندی</label>
                        <select name="category_type" id="category_type" class="form-control">
                            <option value="0">words</option>
                            <option value="1">listening</option>
                            <option value="2">reading</option>
                            <option value="3">grammer</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div id="quick-search-more" class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="parent">کتاب</label>
                        <select name="book_category_id" id="parent" class="form-control selectpicker" data-live-search="true">
                            <option value="0">کتاب</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="sub-category-container">

                    </div>
                </div>
                <input type="hidden" id="parent_category_id" name="category_id" value=0>
                <div class="col-sm-6">
                    <div class="form-group" style="width:100%">
                    <label for="word_id">انتخاب کلمه</label>
                    <br/>
                    <select style="width:100%" disabled="dsiabled" id="select_words" >
                    </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 end text-end">
                    {{--<button type="submit" class="btn btn-primary pull-left">ثبت اطلاعات</button>--}}
                    <a id="send" class="btn btn-primary pull-left">ثبت اطلاعات</a>
                    <br/>
                </div>
            </div>
        </form>
        
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
        var canPostWords = false;
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
                if (selectedCategoryId != 0) ajaxKol(selectedCategoryId);
                $("#parent_category_id").val(selectedCategoryId);
            });

        });//end document

        function ajaxKol(id) {
            canPostWords = false;
            $('#select_words').empty();
            $('#select_words').attr('disabled', 'disabled');
            $.ajax({
                async: false,
                type: "POST",
                url: '{{route('loadSubCat.ajax')}}',
                data: {
                    id: id
                },
                success: function (result) {
                    if (result.length > 0) {
                        $('#sub-category-container').append('<div class="form-group"><label for="exampleInputEmail1">زیر دسته</label><select id="subCat-' + selectCounter + '" onChange="selectChanged(this)" name="category_id" class="form-control subCat"><option value="' + id + '"></option></select></div>');
                        $.each(result, function (index, value) {
                            $('#subCat-' + selectCounter).append('<option value=' + value['id'] + '>' + value["category_name"] + '</option>');
                        });
                        selectCounter++;
                    }else{
                        $('#select_words').removeAttr('disabled');
                        canPostWords = true;
                        ajaxWords(selectedCategoryId);
                    }
                }
            });
        }

        function selectChanged(element) {
            $(element).parent().nextAll().remove();
            selectedCategoryId = $(element).val();
            var firstElementSelected = $(element).children('option:first-child').is(':selected');
            if (!firstElementSelected) {
                ajaxKol(selectedCategoryId);
            }
            $("#parent_category_id").val(selectedCategoryId);
            bookId = $("#sub-category-container").find(">:first-child").find("select").val();
        }
        
        function ajaxWords(categoryId){
             $("#select_words").empty();
            var categoryWordsArray = [];
            var wordIdsArray = [];
            $.ajax({
                async: false,
                type: "POST",
                url: '{{route('getCategoryWords.ajax')}}',
                data: {
                    categoryId: categoryId
                },
                success: function (result) {
                    $.each(result, function (index, object) {
                        var word = object.word;
                        var wordId = object.id;
                        var wordObject = {
                            id: word.id,
                            text : word.word
                        }
                        $("#select_words").append('<option value="'+wordId+'">'+word+'</option>');
                        wordIdsArray.push(wordId);
                        categoryWordsArray.push(wordObject);
                    });
                    $("#select_words").val(wordIdsArray).trigger("change");
                }
            });
        }
      
      
        $('#send').click(function () {
            
            if(!canPostWords){
                swal("حتما باید یکی از تعداد فرزندان دسته بندی ها را انتخاب کنید!!", {
                    icon: "warning",
                });
               return; 
            }
            
            var toSendWordIdsArray = $("#select_words").val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                async: false,
                type: "POST",
                url: '{{route('wordCategories.store')}}',
                data: {
                    wordIdsArray: toSendWordIdsArray,
                    category_id: selectedCategoryId,
                    book_category_id: bookId,
                },
                success: function (result) {
                    console.log(result);
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
                    
                    if (result == "4"){
                        swal("اطلاعات ثبت شد", {
                            icon: "warning",
                        });
                    }

                }
            });
        });
      
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.js">
    </script>
   
    <script>
    
    $(function () {
    
        pageSize = 10
    
        jQuery.fn.select2.amd.require(["select2/data/array", "select2/utils"],
    
        function (ArrayData, Utils) {
            function CustomData($element, options) {
                CustomData.__super__.constructor.call(this, $element, options);
            }
            Utils.Extend(CustomData, ArrayData);
    
            CustomData.prototype.query = function (params, callback) {
            
                results = [];
                if (params.term && params.term !== '') {
                  results = _.filter(wordsArray, function(e) {
                    return e.text.toUpperCase().indexOf(params.term.toUpperCase()) >= 0;
                  });
                } else {
                  results = wordsArray;
                }
    
                if (!("page" in params)) {
                    params.page = 1;
                }
                var data = {};
                data.results = results.slice((params.page - 1) * pageSize, params.page * pageSize);
                data.pagination = {};
                data.pagination.more = params.page * pageSize < results.length;
                callback(data);
            };
    
            $(document).ready(function () {
                $("#select_words").select2({
                    ajax: {},
                    multiple:true,
                    tags: true,
                    dataAdapter: CustomData,
                    dropdownAutoWidth : true
                });
                   
                addSortableAttribute($("#select_words"));
                
                // $("#select_words").on('select2:unselect', function (e) {
                // var optionElement = e.params.data;           
                //     // $("#select_words").remove($(optionElement));
                //       $("#select_words").select2("open");
                //       $("#select_words").select2({
                //             ajax: {},
                //             multiple:true,
                //             tags: true,
                //             dataAdapter: CustomData,
                //             dropdownAutoWidth : true
                //         });
                //     addSortableAttribute($("#select_words"));
                // });
                
                });
                
                
            function addSortableAttribute(element){
                $(element).parent().find("ul.select2-selection__rendered").sortable({ 
                    start: function(event, ui) {
                        ui.item.startPosition = ui.item.index();
                    },
                    stop: function(event, ui) {
                        var El = $(element).find("option").eq(ui.item.startPosition);
                        var cpy = El.clone();
                        if (El.is(':selected'))
                        {
                          cpy.attr("selected","selected");
                        }
                        El.remove();
                        if (ui.item.index() === 0)
                            $(element).prepend(cpy);
                        else
                            $(element).find("option").eq(ui.item.index() -1).after(cpy);
                    }
                });
            }
        })
    });
    </script>
    
    
    
     <script>
       
       $('#category_type').change(function(){
          let value = $("#category_type option:selected").val();
          ajaxCategoryType(value);
          if(value == 0){
              $('#list_type').empty();
              $('#list_type').append('<option value="1">کتاب</option><option value="2">سر فصل</option><option value="2"></option>');
          }else{
              $('#list_type').empty();
              $('#list_type').append('<option value="1">کتاب</option><option value="2">سر فصل</option>');
          }
          console.log(value);
       });

        function ajaxCategoryType(id){
              $.ajax({
                async:false,
                type: "POST",
                url: '{{route('categorytype.ajax')}}',
                data: {
                    id: id
                },
                success: function (result) {
                    $('#parent').empty();
                    $('#parent').append('<option value="0">کتاب</option>');
                    $('#sub-category-container .form-group .subCat').empty();
                    $('#sub-category-container').empty();
                    $.each(result, function (index, value) {
                            $('#parent').append('<option value=' + value['id'] + '>' + value["category_name"] + '</option>');
                        });
                    //console.log(result);
                }
            });
        }
    </script>
    
    
    
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice{
            width:100%;
            text-align:center;
            font-size:16px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
            float:left;
        }
    </style>

@endsection
