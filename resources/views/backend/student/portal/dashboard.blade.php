<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Student Dashboard @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Student Dashboard
            <small>Welcome to your personal dashboard</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Student Portal</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle" src="@if($student->photo ){{ asset('storage/student')}}/{{ $student->photo }} @else {{ asset('images/avatar.jpg')}} @endif" alt="Student profile picture">
                        <h3 class="profile-username text-center">{{ $student->name }}</h3>
                        <p class="text-muted text-center">{{ $registration->class->name }} - {{ $registration->section->name }}</p>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Registration No.</b> <a class="pull-right">{{ $registration->regi_no }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Roll No.</b> <a class="pull-right">{{ $registration->roll_no }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Academic Year</b> <a class="pull-right">{{ $registration->academic_year->title }}</a>
                            </li>
                        </ul>

                        <a href="{{ route('student.portal.profile') }}" class="btn btn-primary btn-block"><b>View Profile</b></a>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                <!-- About Me Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Quick Links</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <a href="{{ route('student.portal.attendance') }}" class="btn btn-app">
                            <i class="fa fa-calendar-check-o"></i> Attendance
                        </a>
                        <a href="{{ route('student.portal.grades') }}" class="btn btn-app">
                            <i class="fa fa-graduation-cap"></i> Grades
                        </a>
                        <a href="{{ route('student.portal.subjects') }}" class="btn btn-app">
                            <i class="fa fa-book"></i> Subjects
                        </a>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
                        <li><a href="#attendance" data-toggle="tab">Recent Attendance</a></li>
                        <li><a href="#exams" data-toggle="tab">Upcoming Exams</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="overview">
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                            <h3>{{ $subjects->count() }}</h3>
                                            <p>Subjects</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-book"></i>
                                        </div>
                                        <a href="{{ route('student.portal.subjects') }}" class="small-box-footer">
                                            More info <i class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- ./col -->
                                <div class="col-md-4">
                                    <!-- small box -->
                                    <div class="small-box bg-green">
                                        <div class="inner">
                                            @php
                                                $presentCount = $recentAttendance->where('present', 1)->count();
                                                $totalCount = $recentAttendance->count();
                                                $attendancePercentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100) : 0;
                                            @endphp
                                            <h3>{{ $attendancePercentage }}<sup style="font-size: 20px">%</sup></h3>
                                            <p>Attendance</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-calendar-check-o"></i>
                                        </div>
                                        <a href="{{ route('student.portal.attendance') }}" class="small-box-footer">
                                            More info <i class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- ./col -->
                                <div class="col-md-4">
                                    <!-- small box -->
                                    <div class="small-box bg-yellow">
                                        <div class="inner">
                                            <h3>{{ $recentExams->count() }}</h3>
                                            <p>Upcoming Exams</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-graduation-cap"></i>
                                        </div>
                                        <a href="{{ route('student.portal.grades') }}" class="small-box-footer">
                                            More info <i class="fa fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- ./col -->
                            </div>
                            <!-- /.row -->

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-info">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Welcome to Student Portal</h3>
                                        </div>
                                        <div class="box-body">
                                            <p>Welcome to your personal student portal. Here you can:</p>
                                            <ul>
                                                <li>View your attendance records</li>
                                                <li>Check your grades and exam results</li>
                                                <li>Access your course materials</li>
                                                <li>Update your profile information</li>
                                            </ul>
                                            <p>Use the quick links on the left to navigate to different sections of the portal.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="attendance">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Recent Attendance</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentAttendance as $attendance)
                                                <tr>
                                                    <td>{{ $attendance->attendance_date }}</td>
                                                    <td>
                                                        @if($attendance->present)
                                                            <span class="label label-success">Present</span>
                                                        @else
                                                            <span class="label label-danger">Absent</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer clearfix">
                                    <a href="{{ route('student.portal.attendance') }}" class="btn btn-sm btn-info btn-flat pull-right">View All Attendance</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="exams">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Upcoming Exams</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Exam Name</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentExams as $exam)
                                                <tr>
                                                    <td>{{ $exam->name }}</td>
                                                    <td>{{ $exam->start_date }}</td>
                                                    <td>{{ $exam->end_date }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer clearfix">
                                    <a href="{{ route('student.portal.grades') }}" class="btn btn-sm btn-info btn-flat pull-right">View All Exams</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
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
