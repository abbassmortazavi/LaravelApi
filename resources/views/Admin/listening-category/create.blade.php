@extends('Admin.master')

@section('style')

    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap_file_field.css') }}">
    <script type="text/javascript" src="{{asset('js/sweetalert.min.js')}}"></script>
@endsection

@section('content')
    <div class="col-lg-12">
        <h2>ثبت لیسنینگ کتگوری </h2>
        <div class="head-section">

        </div>
        <hr>
        @include('Admin.errors.error')
        @include('sweet::alert')
        <form action="{{ route("listeningCategories.store") }}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="title">عنوان</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                </div>
            </div>
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
                    </div>

                </div>
            </div>

            <div class="row gutter-5">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="title">سطح</label>
                         <select class="form-control" name="level">
                             <option value="1">پایه</option>
                             <option value="2">متوسط</option>
                             <option value="3">پیشرفته</option>
                         </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for="description">توضیحات:</label>
                    <textarea class="form-control" rows="5" name="description" id="description">
                        {{ old('description') }}
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
                            <th>Description</th>
                            <th>Image</th>
                            <th>Level</th>
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
    <script>
        let dataTableMe;
        $(function () {
            dataTableMe = $('#table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url('listeningCategoryDataTable') }}',
                    type: "POST",
                    data:
                        function (d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'description', name: 'description'},
                    {data: 'image_path', name: 'image_path'},
                    {data: 'level', name: 'level'},
                    {data: 'is_free', name: 'is_free'},
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let id = $(this).attr('data-id');
            swal({
                title: "آیا مطمئن به حذف هستید؟",
                text: "بعد از حذف این لیسنینگ کتگوری دیگه هیچ وقت بهش دسترسی ندارید!!",
                icon: "error",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {

                        $.ajax({
                            async:false,
                            type: "POST",
                            url: '{{route('deleteListeningCategory.ajax')}}',
                            data: {
                                id: id
                            },
                            success: function (result) {
                                console.log(result);
                                swal("لیسنینگ کتگوری مورد نظر شما با موفقیت حذف شده است!!", {
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
