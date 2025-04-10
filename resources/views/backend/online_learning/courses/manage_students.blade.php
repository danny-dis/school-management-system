<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Online Learning - Manage Students @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Manage Students
            <small>{{ $course->name }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('online_learning.courses')}}"><i class="fa fa-book"></i> Courses</a></li>
            <li><a href="{{URL::route('online_learning.courses.show', $course->id)}}"><i class="fa fa-eye"></i> Course Details</a></li>
            <li class="active">Manage Students</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Enroll Students in {{ $course->name }}</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form id="enrollForm" class="form-horizontal" action="{{ route('online_learning.courses.enroll', $course->id) }}" method="post">
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
                                <label class="col-sm-2 control-label">Class</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">{{ $course->class->name }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="student_ids" class="col-sm-2 control-label">Students*</label>
                                <div class="col-sm-10">
                                    <select name="student_ids[]" id="student_ids" class="form-control select2" multiple="multiple" data-placeholder="Select students to enroll" required>
                                        @foreach($classStudents as $student)
                                            <option value="{{ $student->id }}" {{ in_array($student->id, $enrolledStudentIds) ? 'selected' : '' }}>
                                                {{ $student->student->name }} ({{ $student->roll_no }}) - {{ $student->section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="help-block">Select the students you want to enroll in this course. Hold Ctrl (or Cmd on Mac) to select multiple students.</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="select-all"> Select All Students
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{ route('online_learning.courses.show', $course->id) }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right">Save Changes</button>
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
            
            // Handle select all checkbox
            $('#select-all').click(function() {
                if(this.checked) {
                    // Select all
                    $('#student_ids option').prop('selected', true);
                } else {
                    // Deselect all
                    $('#student_ids option').prop('selected', false);
                }
                $('#student_ids').trigger('change');
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
