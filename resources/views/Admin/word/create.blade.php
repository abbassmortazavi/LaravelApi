@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('css/select2.min.css') }}">
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>--}}
    <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>

@endsection

@section('content')
    <div class="col-lg-12">
        <h2>ثبت کلمه </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        <form action="{{ route("words.store") }}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="row gutter-5">
                  <div class="col-sm-6">
                    <div class="form-group">
                        <label for="word">نام کلمه</label>
                        <input type="text" id="word" class="form-control" name="word"
                               placeholder="word ..." style="direction: ltr;">


                    </div>
                </div>

                
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="phonetic">فونتیک</label>
                 
                            <textarea id="phonetic" class="form-control" name="phonetic"
                                   placeholder= "فونتیک را وارد کنید... " style="direction: ltr;">
                             {{ old('phonetic') }}
                        </textarea>
                               
                    </div>
                </div>

            </div>
            <div id="quick-search-more" class="row gutter-5">
               <div class="col-sm-6">
                    <div class="form-group">
                        <label for="english_meaning">معنی لاتین را وارد کنید</label>
                       
                               
                               
                            <textarea id="english_meaning" class="form-control" style="direction: ltr;" name="english_meaning"
                                  placeholder= "مثال لاتین را وارد کنید..." >
                            {{ old('english_meaning') }}
                        </textarea>
                               
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="persian_meaning">معنی فارسی را وارد کنید</label>
                      <input type="text" id="persian_meaning" class="form-control" name="persian_meaning"
                               placeholder= "Persian Meaning" value="{{ old('persian_meaning') }}">
                    </div>

                </div>
            </div>

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="persian_example">مثال فارسی را وارد کنید</label>
                        <textarea id="persian_example" class="form-control" name="persian_example"
                                  placeholder= "مثال فارسی را وارد کنید..." >
                            {{ old('persian_example') }}
                        </textarea>
                    </div>
                </div>

                 <div class="col-sm-6">
                    <div class="form-group">
                        <label for="english_example">مثال لاتین را وارد کنید</label>
                        <textarea id="english_example" class="form-control" name="english_example"
                                  placeholder="مثال لاتین را وارد کنید..." style="direction: ltr;">
                            {{ old('english_example') }}
                        </textarea>
                    </div>
                </div>
            </div>

            <div class="row gutter-5">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="image">قرار دادن تصویر برای کلمه دیگر</label>
                        <select name="image" id="image" class="form-control">
                            <option></option>
                            @foreach($attachments as $attachment)
                                <option value="{{ $attachment }}">{{ $attachment }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="description">تصویر</label>
                        <div class="img">

                        </div>

                    </div>
                </div>
                 <div class="col-sm-4">
                    <div class="form-group">
                        <a class="btn btn-danger" id="removePic">حذف عکس</a>
                    </div>
                </div>
            </div>

            <div id="quick-search-more" class="row gutter-5">
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
        
          <div class="row">
            <div class="col-md-12">
                <div class="search">
                    <h4 class="text-info" style="margin-bottom: 30px;">جستجو...</h4>
                    <form class="m-form m-form--fit m--margin-bottom-20" id="dataTableSearchForm">
                        <div class="row m--margin-bottom-20">

                            <div class="col-lg-3 m--margin-bottom-10-tablet-and-mobile">
                                <div class="form-group">
                                    <label for="word">نام کلمه</label>
                                    <input type="text" id="word1" class="form-control" name="word1"
                                           placeholder="کلمه رو وارد کنید ...">

                                </div>
                            </div>

                        </div>

                        <div class="m-separator m-separator--md m-separator--dashed"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button class="btn btn-info m-btn m-btn--icon" id="search" style="margin-top: 15px;">
                                        <span style="margin-top: 20px;">
                                            <i class="la la-search"></i>
                                            <span>جستجو</span>
                                        </span>
                                </button>

                                <input type="hidden" value="true" name="search">
                            </div>
                        </div>
                        <div class="m-separator m-separator--md m-separator--dashed"></div>
                    </form>
                </div>

            </div>
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
                            <th>Word</th>
                            <th>Phonetic</th>
                            <th>English Meaning</th>
                            <th>Persian Meaning</th>
                            <th>English Example</th>
                            <th>Persian Example</th>
                            <th>image</th>
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
    <script src="/ckeditor/ckeditor.js"></script>
    <script>
        window.onload = function() {
            CKEDITOR.replace( 'english_example', {
                filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
                language: 'fa',
                contentsCss: "/css/lineHeight.css"

            });
        
            
            CKEDITOR.replace( 'persian_example', {
                filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
                language: 'fa'
            });
            
            CKEDITOR.replace( 'phonetic', {
                filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
                language: 'fa'
            });
            
             CKEDITOR.replace( 'english_meaning', {
                filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
                language: 'fa'
            });
            
           
            
        };

    </script>

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
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //for image show when select every element
            let path = '{{ asset('images/')}}';
            $('#image').change(function () {
                $('.img').empty();
                let image = $("#image option:selected").val();
                $('.img').append('<img id="img" width="100" height="100" class="img-fluid img-rounded" src="'+path+'/'+image+'">');
            });

        });
        
        
        
        
        
        
        //removePic
        $('#removePic').click(function () {
            image = $("#image option:selected").val();

                $.ajax({
                    async:false,
                    type: "POST",
                    url: '{{route('deleteImage.ajax')}}',
                    data: {
                        image: image
                    },
                    success: function (result) {
                        swal("کلمه مورد نظر شما با موفقیت حذف شده است!!", {
                            icon: "success",
                        });
                    }
                });

        });
        
        
        
    </script>

    <script>
        let dataTableMe;
        $(function () {
            dataTableMe = $('#table').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 500,
                //ajax: '{{ url('word') }}',
                ajax: {
                    url: '{{ url('word') }}',
                    type: "POST",
                    data:
                        function (d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                            d.word1 = $('#word1').val();
                        }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'word', name: 'word'},
                    {data: 'phonetic', name: 'phonetic'},
                    {data: 'english_meaning', name: 'english_meaning'},
                    {data: 'persian_meaning', name: 'persian_meaning'},
                    {data: 'english_example', name: 'english_example'},
                    {data: 'persian_example', name: 'persian_example'},
                    {data: 'image_path', name: 'image_path'},
                    {data: 'delete', name: 'delete', orderable: false, searchable: false},
                    {data: 'edit', name: 'edit', orderable: false, searchable: false},
                ],
                //order: [[1, 'asc']]
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
                            url: '{{route('deleteWord.ajax')}}',
                            data: {
                                id: id
                            },
                            success: function (result) {
                                swal("کلمه مورد نظر شما با موفقیت حذف شده است!!", {
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


@endsection
