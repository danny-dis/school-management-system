<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Online Learning - Create Course @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Create Course
            <small>Add a new course</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('online_learning.courses')}}"><i class="fa fa-book"></i> Courses</a></li>
            <li class="active">Create</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Create Course</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form id="courseForm" class="form-horizontal" action="{{ route('online_learning.courses.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="box-body">
                            @if(count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Course Name*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Course Name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="code" class="col-sm-2 control-label">Course Code*</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" placeholder="Course Code" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="class_id" class="col-sm-2 control-label">Class*</label>
                                <div class="col-sm-10">
                                    <select name="class_id" id="class_id" class="form-control select2" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $id => $name)
                                            <option value="{{ $id }}" {{ old('class_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="teacher_id" class="col-sm-2 control-label">Teacher*</label>
                                <div class="col-sm-10">
                                    <select name="teacher_id" id="teacher_id" class="form-control select2" required>
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers as $id => $name)
                                            <option value="{{ $id }}" {{ old('teacher_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description" class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="Course Description">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="syllabus" class="col-sm-2 control-label">Syllabus</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="syllabus" name="syllabus" rows="5" placeholder="Course Syllabus">{{ old('syllabus') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="start_date" class="col-sm-2 control-label">Start Date</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="end_date" class="col-sm-2 control-label">End Date</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="cover_image" class="col-sm-2 control-label">Cover Image</label>
                                <div class="col-sm-10">
                                    <input type="file" id="cover_image" name="cover_image">
                                    <p class="help-block">Upload a cover image for the course (JPEG, PNG, JPG only, max 2MB)</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status" class="col-sm-2 control-label">Status*</label>
                                <div class="col-sm-10">
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="{{ \App\Http\Helpers\AppHelper::ACTIVE }}" {{ old('status') == \App\Http\Helpers\AppHelper::ACTIVE ? 'selected' : '' }}>Active</option>
                                        <option value="{{ \App\Http\Helpers\AppHelper::INACTIVE }}" {{ old('status') == \App\Http\Helpers\AppHelper::INACTIVE ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{ route('online_learning.courses') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right">Create</button>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize select2
            $('.select2').select2();
            
            // Initialize text editors
            $('#description, #syllabus').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['table', ['table']],
                    ['insert', ['link', 'hr']],
                    ['view', ['fullscreen', 'codeview']],
                ]
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
