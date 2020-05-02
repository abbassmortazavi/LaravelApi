@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@endsection

@section('content')
    <div class="col-lg-12">
        <h2>ویرایش لیسنینگ </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')

        <form action="{{ route("listenings.update" , $listening->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="title">عنوان</label>
                        <input type="text" id="title" class="form-control" name="title"
                               placeholder="عنوان..." value="{{$listening->title , old('title') }}">

                    </div>
                </div>
                <div class="col-sm-6">
                    <!--<div class="form-group">
                        <label for="category_id">کتگوری</label>
                        <select class="form-control" name="category_id">
                            @foreach($categories as $category)
                                <option value="{{$category->id}}" {{ $listening->category_id == $listening->id ? "selected":"" }}>{{$category->title}}</option>
                            @endforeach
                        </select>
                    </div>-->
                </div>
            </div>
            <div class="row">
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
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for="description">توضیحات:</label>
                    <textarea class="form-control" rows="5" name="description" id="description">
                        {{$listening->description , old('description')}}
                    </textarea>
                </div>
            </div>

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="description">سوال:</label>
                        <a id="addQuestion" class="btn btn-info btn-round">افزودن سوال</a>
                        <?php $questionCounter = 0; ?>
                        @foreach($questions as $question)
                            <div id="questions">
                                <div class="question-container" id="<?php echo $questionCounter; ?>" style="margin-top: 20px; border: 5px double #1C6EA4; border-radius: 20px 20px 20px 20px; padding:10px;">
                                    <input type="text" class="question-text form-control questions"
                                placeholder="متن سوال..." value="{{$question->question}}">
                                <a onclick="addChoice(this)" class="choice btn btn-warning"
                                   style="margin-left:10px; margin-top:10px;">ایجاد گزینه</a>
                                <a style="margin-top:10px;" class="btn btn-danger" onclick="deleteQuestion(this)">
                                        <span onClick="deleteChoice(this)" role="button" class="fa fa-trash"
                                              style="margin-left: 4px;font-size: 17px;"></span>
                                    Delete Question
                                </a>
                                @foreach($question->choices as $choice)
                                    <div class="form-group choice-container" style="margin:10px 30px;"><label for="description">گزینه:</label>
                                        <input type="text" value="{{$choice->text}}" class="choice-text form-control" name="choiceName">
                                        <div class="form-check">
                                            <input type="radio" name="is-correct<?php echo $questionCounter;?>" class="form-check-input"
                                                   {{$choice->isCorrect == "1" ? "checked":""}} id="is_correct">
                                            <label class="form-check-label" for="exampleCheck1"
                                                   style="margin-top:10px; margin-left:10px;"> Is Correct </label>
                                            <a style="margin-right:10px;" class="btn btn-danger" onclick="deleteChoice(this)"><span onClick="deleteChoice(this)" role="button" class="fa fa-trash" style="margin-left: 4px;font-size: 17px;"></span>حذف گزینه</a>
                                        </div>
                                    </div>

                                @endforeach
                            </div>
                    </div>
                    <?php $questionCounter++; ?>
                    @endforeach
                    <input type="hidden" name="questions" id="questionsForm">
                </div>
            </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="custom-file">
                <label class="custom-file-label" for="customFile">Choose file</label>
                <input type="file" class="custom-file-input" name="file_path" id="customFile">
            </div>
        </div>

        <div class="col-sm-6">
            <div class="custom-file">
                <label class="custom-file-label" for="customFile">Listen to file</label>
                <audio controls>
                    <source src="{{asset('listening/'.$listening->file_path)}}" type="audio/mpeg">
                </audio>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-6 end text-end">
            <button type="submit" class="btn btn-primary pull-left">Update</button>
        </div>
        <!--<div class="col-sm-6 end text-end">-->
        <!--    <button type="button" id="create-object" class="btn btn-primary">Create object</button>-->
        <!--</div>-->
    </div>
    </form>

    </div>
    <!-- /.col-lg-8 -->
@endsection


@section('script')


        <script src="/ckeditor/ckeditor.js"></script>
        <script>
            window.onload = function() {
                CKEDITOR.replace( 'description', {
                    filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
                    language: 'fa'
    
                });
            };
    
        </script>




    <script>
        let questionCounter = <?php echo $questionCounter?>;
        $('#addQuestion').click(function () {
            $('#questions').append('<div class="question-container" id="'+questionCounter+'" style="margin-top: 20px; border: 5px double #1C6EA4; border-radius: 20px 20px 20px 20px; padding:10px;"><input type="text" class="question-text form-control questions" placeholder="متن سوال..." value=""><a onclick="addChoice(this)" class="choice btn btn-warning" style="margin-left:10px; margin-top:10px;">ایجاد گزینه</a><a style="margin-top:10px;" class="btn btn-danger" onclick="deleteQuestion(this)"><span onClick="deleteChoice(this)" role="button" class="fa fa-trash" style="margin-left: 4px;font-size: 17px;"></span>حذف سوال</a></div>');
            questionCounter++;
        });

        function deleteQuestion(elm){
            $(elm).parents(".question-container").remove();
        }
    </script>
    <script>

        let question = "";
        let counter = 0;
        function addChoice(elm){
            let choiceName = "choice" + counter;
            let questionId = $(elm).parents(".question-container").attr("id");
            let radioName = "is-correct" + questionId;
            let questionContainter = $(elm).parent();
            questionContainter.append('<div style="margin:10px 30px;" class="form-group choice-container"><label for="description">گزینه:</label><input type="text" class="choice-text form-control" name="'+choiceName+'" ><div class="form-check"><input type="radio" name="'+radioName+'" class="form-check-input"style="margin-left:5px;" value="1" id="is_correct"><label class="form-check-label" for="exampleCheck1" style="margin-top:10px; margin-left:10px;">  Is Correct </label><a style="margin-right:10px;" class="btn btn-danger" onclick="deleteChoice(this)"><span onClick="deleteChoice(this)" role="button" class="fa fa-trash" style="margin-left: 4px;font-size: 17px;"></span>حذف گزینه</a></div></div>');
            question = $(elm).parent().find('#questions').val();
            counter++;
        }

        function deleteChoice(elm){
            $(elm).parents(".choice-container").remove();
        }

    </script>

    <script>
        let questionId = 1;
        function createObject(){
             var questionArray = [];
            $(".question-container").each(function(i, questionElement){
                var choicesArray = [];
                let choiceId = 1;
                let choiceChosen = false;
                $(questionElement).find(".choice-container").each(function(j,choiceElement){
                    let choiceText = $(choiceElement).find(".choice-text").val();
                    let isCorrect = $(choiceElement).find("input[type=radio]:checked").val();
                    console.log(isCorrect);
                    if(typeof isCorrect === "undefined") {
                        isCorrect="0";
                    }else{
                        choiceChosen = true;
                    }
                    
                    
                    let choice = {
                        id: choiceId,
                        text: choiceText,
                        isCorrect: isCorrect
                    };
                    choicesArray.push(choice);
                    choiceId++;
                });
                let questionText = $(questionElement).find(".question-text").val();
                let question = {
                    id: questionId,
                    question: questionText,
                    choices: choicesArray
                };
                questionArray.push(question);
                questionId++;
                // if(choicesArray.length < 1)
                // {
                //     alert("لطفا برای تمام سوالات گزینه مشخص نمایید");
                //     return;
                // }
                // if(!choiceChosen)
                // {
                //     alert("لطفا یکی از گزینه ها را به عنوان گزینه صحیح هر یک از سوال ها انتخاب کنید");
                //     return;
                // }
            });
            // if(questionArray.length < 1)
            // {
            //     alert("لطفا برای لیسنینگ حداقل یک سوال مشخص نمایید");
            //     return;
            // }
            console.log(questionArray);
            $('#questionsForm').val(JSON.stringify(questionArray));
        }
        $("form").submit(function(){
                createObject();
            });
            
            $('#create-object').click(function(){
                createObject();
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
@endsection
