<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Change Password @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Change Password
            <small>Update your account password</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('student.portal.dashboard')}}"><i class="fa fa-user"></i> Student Portal</a></li>
            <li><a href="{{URL::route('student.portal.profile')}}"><i class="fa fa-user-circle"></i> Profile</a></li>
            <li class="active">Change Password</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Change Your Password</h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <form class="form-horizontal" action="{{ route('student.portal.change_password') }}" method="POST">
                        @csrf
                        <div class="box-body">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            
                            <div class="form-group {{ $errors->has('current_password') ? 'has-error' : '' }}">
                                <label for="current_password" class="col-sm-4 control-label">Current Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password" required>
                                    @if($errors->has('current_password'))
                                        <span class="help-block">{{ $errors->first('current_password') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                                <label for="password" class="col-sm-4 control-label">New Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                                    @if($errors->has('password'))
                                        <span class="help-block">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                                <label for="password_confirmation" class="col-sm-4 control-label">Confirm New Password</label>
                                <div class="col-sm-8">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required>
                                    @if($errors->has('password_confirmation'))
                                        <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-sm-offset-4 col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="show_password"> Show Password
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <a href="{{ route('student.portal.profile') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-info pull-right">Change Password</button>
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
            // Show/hide password
            $('#show_password').change(function() {
                var showPassword = $(this).is(':checked');
                if (showPassword) {
                    $('#current_password, #password, #password_confirmation').attr('type', 'text');
                } else {
                    $('#current_password, #password, #password_confirmation').attr('type', 'password');
                }
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
