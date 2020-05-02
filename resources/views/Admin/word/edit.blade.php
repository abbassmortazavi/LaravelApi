@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('css/select2.min.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@endsection

@section('content')
    <div class="col-lg-12">
        <h2> ویرایش کلمه</h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        <form action="{{ route("words.update" , $word->id) }}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            {{ method_field('PATCH') }}
            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="word">نام کلمه</label>
                        <input type="text" id="word" class="form-control" name="word"
                               placeholder="کلمه رو وارد کنید ..." value="{{ $word->word }}" style="direction: ltr">

                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="phonetic">فونتیک</label>
                                   
                            <textarea id="phonetic" class="form-control" name="phonetic"
                                   placeholder= "فونتیک را وارد کنید... " style="direction: ltr;">
                             {{ $word->phonetic }}
                        </textarea>
                                               
                                               
                    </div>
                </div>

            </div>
            <div id="quick-search-more" class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="english_meaning">معنی لاتین را وارد کنید</label>
      

                        <textarea id="english_meaning" class="form-control" name="english_meaning"
                                          placeholder= "مثال فارسی را وارد کنید..." style="direction: ltr !important;">
                            {{ $word->english_meaning }}
                        </textarea>                          

                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="persian_meaning">معنی فارسی را وارد کنید</label>
                        <input type="text" id="persian_meaning" class="form-control" name="persian_meaning"
                               placeholder="معنی فارسی را وراد کنید" value="{{ $word->persian_meaning }}">
                    </div>

                </div>
            </div>

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="persian_example">مثال فارسی را وارد کنید</label>
                             <textarea id="persian_example" class="form-control" name="persian_example"
                                          placeholder= "مثال فارسی را وارد کنید..." style="direction: rtl !important;">
                            {{ $word->persian_example }}
                        </textarea>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="english_example">مثال لاتین را وارد کنید</label>
                        <textarea id="english_example" class="form-control" name="english_example"
                                  placeholder="مثال لاتین را وارد کنید...">
                            {{ $word->english_example }}
                        </textarea>
                    </div>
                </div>
            </div>

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="image">قرار دادن تصویر برای کلمه دیگ</label>
                        <select name="image" id="image" class="form-control">
                            <option value="no-image.jpg">بدون تصویر</option>
                            @foreach($attachments as $attachment)
                                <option value="{{ $attachment }}" {{ $word->image_path == $attachment ? "selected" : "" }}>{{ $attachment }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="description">تصویر</label>
                        <div class="img">
                            @if($word->image_path)
                                <img id="img" width="100" height="100" class="img-fluid img-rounded" src="{{ asset('images/'.$word->image_path) }}">
                            @endif
                        </div>

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
                                <div class="panel-body">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label style="display: grid;">تصاویر را انتخاب کنید : &nbsp;
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
                contentsCss: "/css/lineHeight.css"

            });

            CKEDITOR.replace( 'persian_example', {
                filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
                contentsCss: "/css/lineHeight.css"
            });
            
            
            CKEDITOR.replace( 'phonetic', {
                filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',

            });
            
            CKEDITOR.replace( 'english_meaning', {
                filebrowserUploadUrl: '{{ route('upload',['_token' => csrf_token() ]) }}',
                contentsCss: "/css/lineHeight.css"

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
                $('.img').append('<img id="img" width="100" height="100" class="img-fluid img-rounded" src="' + path + '/' + image + '">');
            });

        });
    </script>




@endsection
