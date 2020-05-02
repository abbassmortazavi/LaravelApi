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
        <form action="{{ route("uploadImageInDirectory") }}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
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
        


    
    </div>
    <!-- /.col-lg-8 -->
@endsection


@section('script')
    <script type="text/javascript" src="{{asset('js/bootstrap_file_field.js')}}"></script>
    <script src="/ckeditor/ckeditor.js"></script>
 

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

  


@endsection
