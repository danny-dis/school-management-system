<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Parent Profile @endsection
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
            <li><a href="{{URL::route('parent.portal.dashboard')}}"><i class="fa fa-users"></i> Parent Portal</a></li>
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
                        <a href="{{ route('parent.portal.dashboard') }}" class="btn btn-app">
                            <i class="fa fa-dashboard"></i> Dashboard
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
                        <li class="active"><a href="#profile" data-toggle="tab">Profile</a></li>
                        <li><a href="#children" data-toggle="tab">My Children</a></li>
                        <li><a href="#settings" data-toggle="tab">Settings</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="active tab-pane" id="profile">
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
                                                <dd>{{ $user->name }}</dd>
                                                
                                                <dt>Username</dt>
                                                <dd>{{ $user->username }}</dd>
                                                
                                                <dt>Email</dt>
                                                <dd>{{ $user->email }}</dd>
                                                
                                                <dt>Phone</dt>
                                                <dd>{{ $user->phone_no }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="children">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">My Children</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        @foreach($children as $child)
                                            <div class="col-md-6">
                                                <div class="box box-widget widget-user-2">
                                                    <div class="widget-user-header bg-yellow">
                                                        <div class="widget-user-image">
                                                            <img class="img-circle" src="@if($child->photo ){{ asset('storage/student')}}/{{ $child->photo }} @else {{ asset('images/avatar.jpg')}} @endif" alt="Student profile picture">
                                                        </div>
                                                        <h3 class="widget-user-username">{{ $child->name }}</h3>
                                                        <h5 class="widget-user-desc">Student</h5>
                                                    </div>
                                                    <div class="box-footer no-padding">
                                                        <ul class="nav nav-stacked">
                                                            <li><a href="#">Date of Birth <span class="pull-right">{{ $child->dob }}</span></a></li>
                                                            <li><a href="#">Gender <span class="pull-right">{{ $child->gender }}</span></a></li>
                                                            <li><a href="#">Phone <span class="pull-right">{{ $child->phone_no }}</span></a></li>
                                                            <li><a href="#">Email <span class="pull-right">{{ $child->email }}</span></a></li>
                                                            <li>
                                                                <a href="{{ route('parent.portal.child_details', $child->id) }}" class="btn btn-primary btn-block">
                                                                    <i class="fa fa-eye"></i> View Details
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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
                                                <a href="{{ route('parent.portal.change_password') }}" class="btn btn-primary">
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
                    <form action="{{ route('parent.portal.update_profile') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Update Profile</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                            </div>
                            <div class="form-group">
                                <label for="phone_no">Phone Number</label>
                                <input type="text" class="form-control" id="phone_no" name="phone_no" value="{{ $user->phone_no }}" required>
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
