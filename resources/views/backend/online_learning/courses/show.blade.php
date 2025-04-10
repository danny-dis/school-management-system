<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Online Learning - Course Details @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Course Details
            <small>{{ $course->name }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('online_learning.courses')}}"><i class="fa fa-book"></i> Courses</a></li>
            <li class="active">Details</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <!-- Course Info Box -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Course Information</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body box-profile">
                        @if($course->cover_image)
                            <img class="img-responsive" src="{{ asset('storage/courses/'.$course->cover_image) }}" alt="Course cover image">
                        @else
                            <img class="img-responsive" src="{{ asset('images/course-default.jpg') }}" alt="Default course image">
                        @endif
                        <h3 class="profile-username text-center">{{ $course->name }}</h3>
                        <p class="text-muted text-center">{{ $course->code }}</p>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Class</b> <a class="pull-right">{{ $course->class->name }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Teacher</b> <a class="pull-right">{{ $course->teacher->name }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Status</b> 
                                <span class="pull-right">
                                    @if($course->status == \App\Http\Helpers\AppHelper::ACTIVE)
                                        <span class="label label-success">Active</span>
                                    @else
                                        <span class="label label-warning">Inactive</span>
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Start Date</b> <a class="pull-right">{{ $course->start_date ?? 'Not set' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>End Date</b> <a class="pull-right">{{ $course->end_date ?? 'Not set' }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Lessons</b> <a class="pull-right">{{ $course->lessons->count() }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Assignments</b> <a class="pull-right">{{ $course->assignments->count() }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Students</b> <a class="pull-right">{{ $students->count() }}</a>
                            </li>
                        </ul>

                        <div class="btn-group btn-group-justified">
                            <a href="{{ route('online_learning.courses.edit', $course->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
                            <a href="{{ route('online_learning.lessons.index', $course->id) }}" class="btn btn-info"><i class="fa fa-book"></i> Lessons</a>
                            <a href="{{ route('online_learning.assignments.index', $course->id) }}" class="btn btn-warning"><i class="fa fa-tasks"></i> Assignments</a>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
            <div class="col-md-8">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#description" data-toggle="tab">Description</a></li>
                        <li><a href="#syllabus" data-toggle="tab">Syllabus</a></li>
                        <li><a href="#students" data-toggle="tab">Students</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="description">
                            <div class="box-body">
                                {!! $course->description ?? 'No description available.' !!}
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="syllabus">
                            <div class="box-body">
                                {!! $course->syllabus ?? 'No syllabus available.' !!}
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="students">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-group pull-right">
                                            <a href="{{ route('online_learning.courses.students', $course->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-users"></i> Manage Students</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @if($students->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Name</th>
                                                            <th>Roll</th>
                                                            <th>Section</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($students as $student)
                                                            <tr>
                                                                <td>{{ $student->student->id }}</td>
                                                                <td>{{ $student->student->name }}</td>
                                                                <td>{{ $student->roll_no }}</td>
                                                                <td>{{ $student->section->name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <h4><i class="icon fa fa-info"></i> Note!</h4>
                                                No students enrolled in this course yet.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->

                <!-- Recent Lessons Box -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Recent Lessons</h3>
                        <div class="box-tools pull-right">
                            <a href="{{ route('online_learning.lessons.index', $course->id) }}" class="btn btn-box-tool"><i class="fa fa-list"></i> View All</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        @if($course->lessons->count() > 0)
                            <ul class="products-list product-list-in-box">
                                @foreach($course->lessons->sortBy('order')->take(5) as $lesson)
                                    <li class="item">
                                        <div class="product-img">
                                            <i class="fa fa-book fa-3x text-info"></i>
                                        </div>
                                        <div class="product-info">
                                            <a href="{{ route('online_learning.lessons.show', [$course->id, $lesson->id]) }}" class="product-title">
                                                {{ $lesson->title }}
                                                @if($lesson->status == \App\Http\Helpers\AppHelper::ACTIVE)
                                                    <span class="label label-success pull-right">Published</span>
                                                @else
                                                    <span class="label label-warning pull-right">Draft</span>
                                                @endif
                                            </a>
                                            <span class="product-description">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($lesson->description), 100) }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-info">
                                <h4><i class="icon fa fa-info"></i> Note!</h4>
                                No lessons created for this course yet.
                            </div>
                        @endif
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <a href="{{ route('online_learning.lessons.create', $course->id) }}" class="btn btn-info"><i class="fa fa-plus"></i> Add New Lesson</a>
                    </div>
                    <!-- /.box-footer -->
                </div>
                <!-- /.box -->

                <!-- Recent Assignments Box -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Recent Assignments</h3>
                        <div class="box-tools pull-right">
                            <a href="{{ route('online_learning.assignments.index', $course->id) }}" class="btn btn-box-tool"><i class="fa fa-list"></i> View All</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        @if($course->assignments->count() > 0)
                            <ul class="products-list product-list-in-box">
                                @foreach($course->assignments->sortByDesc('created_at')->take(5) as $assignment)
                                    <li class="item">
                                        <div class="product-img">
                                            <i class="fa fa-tasks fa-3x text-warning"></i>
                                        </div>
                                        <div class="product-info">
                                            <a href="{{ route('online_learning.assignments.show', [$course->id, $assignment->id]) }}" class="product-title">
                                                {{ $assignment->title }}
                                                <span class="label label-info pull-right">Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</span>
                                            </a>
                                            <span class="product-description">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($assignment->description), 100) }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-info">
                                <h4><i class="icon fa fa-info"></i> Note!</h4>
                                No assignments created for this course yet.
                            </div>
                        @endif
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-center">
                        <a href="{{ route('online_learning.assignments.create', $course->id) }}" class="btn btn-warning"><i class="fa fa-plus"></i> Add New Assignment</a>
                    </div>
                    <!-- /.box-footer -->
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
            // Any JavaScript specific to this page can go here
        });
    </script>
@endsection
<!-- END PAGE JS-->
