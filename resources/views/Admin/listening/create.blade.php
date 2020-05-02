@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>
@endsection

@section('content')
    <div class="col-lg-12">
        <h2>ثبت لیسنینگ </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        <form method="post" id="sendForm" enctype="multipart/form-data">
            {{csrf_field()}}

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="title">عنوان</label>
                        <input type="text" id="title" class="form-control" name="title"
                               placeholder="عنوان..." value="{{ old('title') }}">

                    </div>
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

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="description">سوال:</label>
                        <a id="addQuestion" class="btn btn-info btn-round">افزودن سوال</a>
                        <div id="questions">

                        </div>
                        <input type="hidden" name="questions" id="questionsForm">
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for="description">توضیحات:</label>
                    <textarea class="form-control" rows="5" name="description" id="description">{{old('description')}}</textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="custom-file">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                        <input type="file" class="custom-file-input" name="file_path" id="customFile">
                    </div>
                </div>

            </div>



            <div class="row">
                {{-- <div class="col-sm-6 end text-end">
                    <button type="submit" class="btn btn-primary pull-left">ثبت اطلاعات</button>
                </div> --}}
                <div class="col-sm-6 end text-end">
                    <button type="button" id="sendData" class="btn btn-primary">Create</button>
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
                            <th>Title</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Free</th>
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
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>

    <script>
        let questionCounter = 0;
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
        function createObject()
        {
            //$("#create-object").click(function(){
                var questionArray = [];
                $(".question-container").each(function(i, questionElement){
                    var choicesArray = [];
                    let choiceId = 1;
                    let choiceChosen = false;
                    $(questionElement).find(".choice-container").each(function(j,choiceElement){
                        let choiceText = $(choiceElement).find(".choice-text").val();
                        let isCorrect = $(choiceElement).find("input[type=radio]:checked").val();
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
            //});
        }
        $("form").submit(function(){
            createObject();
        });
    </script>

    <script>
        let dataTableMe;
        $(function () {
            dataTableMe = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url('listeningDataTable') }}',
                    type: "POST",
                    data:
                        function (d) {
                            d._token = $('meta[name="csrf-token"]').attr('content')
                        }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'category_id', name: 'category_id'},
                    {data: 'description', name: 'description'},
                    {data: 'is_free', name: 'is_free'},
                    {data: 'is_published', name: 'is_published'},
                    {data: 'delete', name: 'delete', orderable: false, searchable: false},
                    {data: 'edit', name: 'edit', orderable: false, searchable: false},
                ],
                order: [[1, 'asc']]
            });
        });

        $(document).on("click", "#delete",function () {
            let id = $(this).attr('data-id');
            swal({
                title: "آیا مطمئن به حذف هستید؟",
                text: "بعد از حذف این لیسنینگ دیگه هیچ وقت بهش دسترسی ندارید!!",
                icon: "error",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {

                        $.ajax({
                            async:false,
                            type: "POST",
                            url: '{{route('deleteListening.ajax')}}',
                            data: {
                                id: id
                            },
                            success: function (result) {
                                console.log(result);
                                swal("لیسنینگ  مورد نظر شما با موفقیت حذف شده است!!", {
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

    <script>
        let image = '';
        $('#customFile').change(function(e){
            
             file = e.target.files[0];
                
                let reader = new FileReader();
                reader.onloadend = (file) => {
                    image = reader.result;
                   //console.log(image);
                };
                reader.readAsDataURL(file);
        });





        $('#sendData').click(function(){
            createObject();
            //console.log(image);
            let title = $('#title').val();
            let category_id = $('#parent_category_id').val();
            let category_type = $('#category_type').val();
            let book_category_id = $('#parent').val();
            let questions = $('#questionsForm').val();
            let description = CKEDITOR.instances.description.getData();

            $.ajax({
                async:false,
                url: '{{ route('listenings.store') }}',
                method: "POST",
                data: {
                    title:title,category_id:category_id,category_type:category_type,description:description,image:image,book_category_id:book_category_id,questions:questions
                },
                success:function(data){
                    console.log(data);
                    if (data == 1){
                        swal("دسته بندی مورد نظر خود را انتخاب نکرده اید!!", {
                            icon: "warning",
                        });
                    }
                    if (data == 2){
                        swal("یکی از دسته بندی هارو انتخاب کنید!!", {
                            icon: "warning",
                        });
                    }
                    if (data == 3){
                        swal("فیلد عنوان نباید خالی باشد!!", {
                            icon: "warning",
                        });
                    }
                    if (data == 0){
                        swal("اطلاعات با موفقیت ثبت شده است!!", {
                            icon: "warning",
                        });
                    }
                }
            });
        
            //console.log(formData1);
        });
        
        


    </script>

@endsection
