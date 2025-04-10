<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Student Profile @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            My Profile
            <small>View and update your profile information</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('student.portal.dashboard')}}"><i class="fa fa-user"></i> Student Portal</a></li>
            <li class="active">Profile</li>
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
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

                <!-- About Me Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">About Me</h3>
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
                        <li class="active"><a href="#details" data-toggle="tab">Personal Details</a></li>
                        <li><a href="#family" data-toggle="tab">Family Information</a></li>
                        <li><a href="#academic" data-toggle="tab">Academic Information</a></li>
                        <li><a href="#settings" data-toggle="tab">Settings</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="details">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Personal Information</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-toggle="modal" data-target="#modal-update-profile">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="dl-horizontal">
                                                <dt>Full Name</dt>
                                                <dd>{{ $student->name }}</dd>
                                                
                                                <dt>Nick Name</dt>
                                                <dd>{{ $student->nick_name ?? 'N/A' }}</dd>
                                                
                                                <dt>Date of Birth</dt>
                                                <dd>{{ $student->dob }}</dd>
                                                
                                                <dt>Gender</dt>
                                                <dd>{{ $student->gender }}</dd>
                                                
                                                <dt>Religion</dt>
                                                <dd>{{ $student->religion }}</dd>
                                                
                                                <dt>Blood Group</dt>
                                                <dd>{{ $student->blood_group }}</dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="dl-horizontal">
                                                <dt>Nationality</dt>
                                                <dd>{{ $student->nationality }}</dd>
                                                
                                                <dt>Email</dt>
                                                <dd>{{ $student->email }}</dd>
                                                
                                                <dt>Phone</dt>
                                                <dd>{{ $student->phone_no }}</dd>
                                                
                                                <dt>Present Address</dt>
                                                <dd>{{ $student->present_address }}</dd>
                                                
                                                <dt>Permanent Address</dt>
                                                <dd>{{ $student->permanent_address }}</dd>
                                                
                                                <dt>Extra Activities</dt>
                                                <dd>{{ $student->extra_activity ?? 'N/A' }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="family">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Family Information</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Father's Information</h3>
                                                </div>
                                                <div class="box-body">
                                                    <dl class="dl-horizontal">
                                                        <dt>Name</dt>
                                                        <dd>{{ $student->father_name }}</dd>
                                                        
                                                        <dt>Phone</dt>
                                                        <dd>{{ $student->father_phone_no }}</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Mother's Information</h3>
                                                </div>
                                                <div class="box-body">
                                                    <dl class="dl-horizontal">
                                                        <dt>Name</dt>
                                                        <dd>{{ $student->mother_name }}</dd>
                                                        
                                                        <dt>Phone</dt>
                                                        <dd>{{ $student->mother_phone_no }}</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Guardian's Information</h3>
                                                </div>
                                                <div class="box-body">
                                                    <dl class="dl-horizontal">
                                                        <dt>Name</dt>
                                                        <dd>{{ $student->guardian }}</dd>
                                                        
                                                        <dt>Phone</dt>
                                                        <dd>{{ $student->guardian_phone_no }}</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="box box-solid">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">SMS Notification</h3>
                                                </div>
                                                <div class="box-body">
                                                    <dl class="dl-horizontal">
                                                        <dt>SMS Receive Number</dt>
                                                        <dd>{{ $student->sms_receive_no }}</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="academic">
                            <div class="box box-solid">
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
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="settings">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Account Settings</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" class="form-control" value="{{ $user->username }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <a href="{{ route('student.portal.change_password') }}" class="btn btn-primary">
                                                    <i class="fa fa-key"></i> Change Password
                                                </a>
                                            </div>
                                        </div>
                                    </div>
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
        
        <!-- Update Profile Modal -->
        <div class="modal fade" id="modal-update-profile">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('student.portal.update_profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Update Profile</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $student->email }}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone_no">Phone Number</label>
                                <input type="text" class="form-control" id="phone_no" name="phone_no" value="{{ $student->phone_no }}" required>
                            </div>
                            <div class="form-group">
                                <label for="present_address">Present Address</label>
                                <textarea class="form-control" id="present_address" name="present_address" rows="3" required>{{ $student->present_address }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="permanent_address">Permanent Address</label>
                                <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3" required>{{ $student->permanent_address }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="photo">Profile Photo</label>
                                <input type="file" id="photo" name="photo">
                                <p class="help-block">Upload a new profile photo (JPEG, PNG, JPG only, max 2MB)</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
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
