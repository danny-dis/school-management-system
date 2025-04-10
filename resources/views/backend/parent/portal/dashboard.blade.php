<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Parent Dashboard @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Parent Dashboard
            <small>Welcome to your parent portal</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Parent Portal</li>
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
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('images/avatar.jpg') }}" alt="Parent profile picture">
                        <h3 class="profile-username text-center">{{ $user->name }}</h3>
                        <p class="text-muted text-center">Parent</p>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Email</b> <a class="pull-right">{{ $user->email }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Phone</b> <a class="pull-right">{{ $user->phone_no }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Children</b> <a class="pull-right">{{ $children->count() }}</a>
                            </li>
                        </ul>

                        <a href="{{ route('parent.portal.profile') }}" class="btn btn-primary btn-block"><b>View Profile</b></a>
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
                        <a href="{{ route('parent.portal.profile') }}" class="btn btn-app">
                            <i class="fa fa-user"></i> Profile
                        </a>
                        <a href="{{ route('parent.portal.change_password') }}" class="btn btn-app">
                            <i class="fa fa-key"></i> Password
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
                        <li><a href="#children" data-toggle="tab">My Children</a></li>
                        <li><a href="#exams" data-toggle="tab">Upcoming Exams</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="overview">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-info">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Welcome to Parent Portal</h3>
                                        </div>
                                        <div class="box-body">
                                            <p>Welcome to your personal parent portal. Here you can:</p>
                                            <ul>
                                                <li>View your children's attendance records</li>
                                                <li>Check your children's grades and exam results</li>
                                                <li>Access your children's course materials</li>
                                                <li>Update your profile information</li>
                                            </ul>
                                            <p>Use the tabs above to navigate to different sections of the portal.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="children">
                            <div class="row">
                                @foreach($registrations as $registration)
                                    <div class="col-md-6">
                                        <div class="box box-widget widget-user">
                                            <div class="widget-user-header bg-aqua-active">
                                                <h3 class="widget-user-username">{{ $registration->student->name }}</h3>
                                                <h5 class="widget-user-desc">{{ $registration->class->name }} - {{ $registration->section->name }}</h5>
                                            </div>
                                            <div class="widget-user-image">
                                                <img class="img-circle" src="@if($registration->student->photo ){{ asset('storage/student')}}/{{ $registration->student->photo }} @else {{ asset('images/avatar.jpg')}} @endif" alt="Student profile picture">
                                            </div>
                                            <div class="box-footer">
                                                <div class="row">
                                                    <div class="col-sm-4 border-right">
                                                        <div class="description-block">
                                                            <h5 class="description-header">{{ $registration->roll_no }}</h5>
                                                            <span class="description-text">ROLL NO</span>
                                                        </div>
                                                        <!-- /.description-block -->
                                                    </div>
                                                    <!-- /.col -->
                                                    <div class="col-sm-4 border-right">
                                                        <div class="description-block">
                                                            <h5 class="description-header">{{ $registration->regi_no }}</h5>
                                                            <span class="description-text">REG NO</span>
                                                        </div>
                                                        <!-- /.description-block -->
                                                    </div>
                                                    <!-- /.col -->
                                                    <div class="col-sm-4">
                                                        <div class="description-block">
                                                            @php
                                                                $attendances = $recentAttendances[$registration->id];
                                                                $presentCount = $attendances->where('present', 1)->count();
                                                                $totalCount = $attendances->count();
                                                                $attendancePercentage = $totalCount > 0 ? round(($presentCount / $totalCount) * 100) : 0;
                                                            @endphp
                                                            <h5 class="description-header">{{ $attendancePercentage }}%</h5>
                                                            <span class="description-text">ATTENDANCE</span>
                                                        </div>
                                                        <!-- /.description-block -->
                                                    </div>
                                                    <!-- /.col -->
                                                </div>
                                                <!-- /.row -->
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="description-block">
                                                            <a href="{{ route('parent.portal.child_details', $registration->student->id) }}" class="btn btn-block btn-primary">
                                                                <i class="fa fa-eye"></i> View Details
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
