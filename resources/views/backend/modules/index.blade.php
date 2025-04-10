<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Module Management @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Module Management
            <small>Enable or disable system modules</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Module Management</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">System Modules</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Module Name</th>
                                        <th width="40%">Description</th>
                                        <th width="10%">Version</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $key => $module)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $module['name'] }}</td>
                                            <td>{{ $module['description'] }}</td>
                                            <td>{{ $module['version'] }}</td>
                                            <td>
                                                @if($module['status'])
                                                    <span class="label label-success">Enabled</span>
                                                @else
                                                    <span class="label label-danger">Disabled</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($module['status'])
                                                    <form action="{{ route('modules.disable') }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="module_key" value="{{ $key }}">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to disable this module?');">
                                                            <i class="fa fa-power-off"></i> Disable
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('modules.enable') }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <input type="hidden" name="module_key" value="{{ $key }}">
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fa fa-check"></i> Enable
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="alert alert-info">
                            <h4><i class="icon fa fa-info"></i> Note!</h4>
                            <p>Some modules have dependencies on other modules. You cannot enable a module if its dependencies are not enabled.</p>
                            <p>Similarly, you cannot disable a module if other enabled modules depend on it.</p>
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Module Information</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="callout callout-info">
                            <h4>About Modules</h4>
                            <p>CloudSchool is built with a modular architecture that allows you to enable or disable specific features based on your needs.</p>
                            <p>Each module provides a set of related features that can be managed independently.</p>
                            <p>This modular approach allows for a more customized experience and better performance by only loading the features you need.</p>
                            <p class="text-muted">Powered by Zophlic - Advanced School Management Solutions</p>
                        </div>
                    </div>
                    <!-- /.box-body -->
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
