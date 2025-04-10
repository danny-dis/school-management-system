<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Child Details @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Child Details
            <small>View your child's information</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('parent.portal.dashboard')}}"><i class="fa fa-users"></i> Parent Portal</a></li>
            <li class="active">Child Details</li>
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

                        <a href="{{ route('parent.portal.child_attendance', $student->id) }}" class="btn btn-primary btn-block"><b>View Attendance</b></a>
                        <a href="{{ route('parent.portal.child_grades', $student->id) }}" class="btn btn-success btn-block"><b>View Grades</b></a>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                <!-- About Me Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">About {{ $student->name }}</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <strong><i class="fa fa-calendar margin-r-5"></i> Date of Birth</strong>
                        <p class="text-muted">{{ $student->dob }}</p>

                        <hr>

                        <strong><i class="fa fa-venus-mars margin-r-5"></i> Gender</strong>
                        <p class="text-muted">{{ $student->gender }}</p>

                        <hr>

                        <strong><i class="fa fa-globe margin-r-5"></i> Nationality</strong>
                        <p class="text-muted">{{ $student->nationality }}</p>

                        <hr>

                        <strong><i class="fa fa-tint margin-r-5"></i> Blood Group</strong>
                        <p class="text-muted">{{ $student->blood_group }}</p>

                        <hr>

                        <strong><i class="fa fa-phone margin-r-5"></i> Phone</strong>
                        <p class="text-muted">{{ $student->phone_no }}</p>

                        <hr>

                        <strong><i class="fa fa-envelope margin-r-5"></i> Email</strong>
                        <p class="text-muted">{{ $student->email }}</p>
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
                        <li><a href="#attendance" data-toggle="tab">Attendance</a></li>
                        <li><a href="#grades" data-toggle="tab">Grades</a></li>
                        <li><a href="#subjects" data-toggle="tab">Subjects</a></li>
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
                                    </div>
                                </div>
                                <!-- ./col -->
                                <div class="col-md-4">
                                    <!-- small box -->
                                    <div class="small-box bg-green">
                                        <div class="inner">
                                            <h3>{{ $attendancePercentage }}<sup style="font-size: 20px">%</sup></h3>
                                            <p>Attendance</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-calendar-check-o"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- ./col -->
                                <div class="col-md-4">
                                    <!-- small box -->
                                    <div class="small-box bg-yellow">
                                        <div class="inner">
                                            @php
                                                $latestResult = $results->first();
                                                $gpa = $latestResult ? $latestResult->grade_point : 'N/A';
                                            @endphp
                                            <h3>{{ $gpa }}</h3>
                                            <p>Latest GPA</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-graduation-cap"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- ./col -->
                            </div>
                            <!-- /.row -->

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-info">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Academic Information</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <dl class="dl-horizontal">
                                                        <dt>Class</dt>
                                                        <dd>{{ $registration->class->name }}</dd>
                                                        
                                                        <dt>Section</dt>
                                                        <dd>{{ $registration->section->name }}</dd>
                                                        
                                                        <dt>Roll Number</dt>
                                                        <dd>{{ $registration->roll_no }}</dd>
                                                        
                                                        <dt>Registration Number</dt>
                                                        <dd>{{ $registration->regi_no }}</dd>
                                                    </dl>
                                                </div>
                                                <div class="col-md-6">
                                                    <dl class="dl-horizontal">
                                                        <dt>Academic Year</dt>
                                                        <dd>{{ $registration->academic_year->title }}</dd>
                                                        
                                                        <dt>Shift</dt>
                                                        <dd>{{ $registration->shift }}</dd>
                                                        
                                                        <dt>Card Number</dt>
                                                        <dd>{{ $registration->card_no }}</dd>
                                                        
                                                        <dt>Board Registration No.</dt>
                                                        <dd>{{ $registration->board_regi_no ?? 'N/A' }}</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="attendance">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Attendance Statistics</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Days</span>
                                                    <span class="info-box-number">{{ $totalDays }}</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Present Days</span>
                                                    <span class="info-box-number">{{ $presentDays }}</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-red"><i class="fa fa-times-circle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Absent Days</span>
                                                    <span class="info-box-number">{{ $absentDays }}</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-md-3 col-sm-6 col-xs-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-yellow"><i class="fa fa-percent"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Attendance Rate</span>
                                                    <span class="info-box-number">{{ $attendancePercentage }}%</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <a href="{{ route('parent.portal.child_attendance', $student->id) }}" class="btn btn-primary">View Full Attendance</a>
                                </div>
                            </div>
                            
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
                                                <th>Day</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attendances->take(5) as $attendance)
                                                <tr>
                                                    <td>{{ $attendance->attendance_date }}</td>
                                                    <td>{{ date('l', strtotime($attendance->attendance_date)) }}</td>
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
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="grades">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Exam Results</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    @if($results->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Exam</th>
                                                        <th>Total Marks</th>
                                                        <th>GPA</th>
                                                        <th>Grade</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($results as $result)
                                                        <tr>
                                                            <td>{{ $exams->where('id', $result->exam_id)->first()->name }}</td>
                                                            <td>{{ $result->total_marks }}</td>
                                                            <td>{{ $result->grade_point }}</td>
                                                            <td>{{ $result->grade }}</td>
                                                            <td>
                                                                @if($result->result_status == 1)
                                                                    <span class="label label-success">Passed</span>
                                                                @else
                                                                    <span class="label label-danger">Failed</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <h4><i class="icon fa fa-info"></i> Note!</h4>
                                            No results found for this student.
                                        </div>
                                    @endif
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <a href="{{ route('parent.portal.child_grades', $student->id) }}" class="btn btn-primary">View Detailed Grades</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="subjects">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Subjects</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        @foreach($subjects as $subject)
                                            <div class="col-md-4">
                                                <div class="box box-widget">
                                                    <div class="box-header with-border bg-aqua">
                                                        <h3 class="box-title">{{ $subject->name }}</h3>
                                                    </div>
                                                    <div class="box-body">
                                                        <p><strong>Code:</strong> {{ $subject->code }}</p>
                                                        <p><strong>Type:</strong> {{ $subject->type }}</p>
                                                        <p><strong>Full Mark:</strong> {{ $subject->full_mark }}</p>
                                                        <p><strong>Pass Mark:</strong> {{ $subject->pass_mark }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- /.box-body -->
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
