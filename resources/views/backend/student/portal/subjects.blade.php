<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Student Subjects @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Subjects & Course Materials
            <small>View your subjects and course materials</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('student.portal.dashboard')}}"><i class="fa fa-user"></i> Student Portal</a></li>
            <li class="active">Subjects</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">My Subjects</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            @foreach($subjects as $subject)
                                <div class="col-md-4">
                                    <div class="box box-widget widget-user">
                                        <div class="widget-user-header bg-aqua-active">
                                            <h3 class="widget-user-username">{{ $subject->name }}</h3>
                                            <h5 class="widget-user-desc">{{ $subject->code }}</h5>
                                        </div>
                                        <div class="widget-user-image">
                                            <img class="img-circle" src="{{ asset('images/subject.png') }}" alt="Subject Image">
                                        </div>
                                        <div class="box-footer">
                                            <div class="row">
                                                <div class="col-sm-4 border-right">
                                                    <div class="description-block">
                                                        <h5 class="description-header">{{ $subject->type }}</h5>
                                                        <span class="description-text">TYPE</span>
                                                    </div>
                                                    <!-- /.description-block -->
                                                </div>
                                                <!-- /.col -->
                                                <div class="col-sm-4 border-right">
                                                    <div class="description-block">
                                                        <h5 class="description-header">{{ $subject->class->name }}</h5>
                                                        <span class="description-text">CLASS</span>
                                                    </div>
                                                    <!-- /.description-block -->
                                                </div>
                                                <!-- /.col -->
                                                <div class="col-sm-4">
                                                    <div class="description-block">
                                                        <h5 class="description-header">{{ $subject->full_mark }}</h5>
                                                        <span class="description-text">FULL MARK</span>
                                                    </div>
                                                    <!-- /.description-block -->
                                                </div>
                                                <!-- /.col -->
                                            </div>
                                            <!-- /.row -->
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="description-block">
                                                        <button type="button" class="btn btn-block btn-info btn-sm" data-toggle="modal" data-target="#modal-subject-{{ $subject->id }}">
                                                            <i class="fa fa-info-circle"></i> View Details
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.widget-user -->
                                </div>
                                <!-- /.col -->
                                
                                <!-- Subject Details Modal -->
                                <div class="modal fade" id="modal-subject-{{ $subject->id }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title">{{ $subject->name }} ({{ $subject->code }})</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="box box-solid">
                                                            <div class="box-header with-border">
                                                                <h3 class="box-title">Subject Information</h3>
                                                            </div>
                                                            <div class="box-body">
                                                                <dl class="dl-horizontal">
                                                                    <dt>Subject Name</dt>
                                                                    <dd>{{ $subject->name }}</dd>
                                                                    
                                                                    <dt>Subject Code</dt>
                                                                    <dd>{{ $subject->code }}</dd>
                                                                    
                                                                    <dt>Type</dt>
                                                                    <dd>{{ $subject->type }}</dd>
                                                                    
                                                                    <dt>Class</dt>
                                                                    <dd>{{ $subject->class->name }}</dd>
                                                                    
                                                                    <dt>Full Mark</dt>
                                                                    <dd>{{ $subject->full_mark }}</dd>
                                                                    
                                                                    <dt>Pass Mark</dt>
                                                                    <dd>{{ $subject->pass_mark }}</dd>
                                                                    
                                                                    <dt>Subject Author</dt>
                                                                    <dd>{{ $subject->author }}</dd>
                                                                </dl>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="box box-solid">
                                                            <div class="box-header with-border">
                                                                <h3 class="box-title">Subject Description</h3>
                                                            </div>
                                                            <div class="box-body">
                                                                <p>{{ $subject->description ?? 'No description available.' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="box box-solid">
                                                            <div class="box-header with-border">
                                                                <h3 class="box-title">Teachers</h3>
                                                            </div>
                                                            <div class="box-body">
                                                                @if($subject->teachers->count() > 0)
                                                                    <ul class="list-unstyled">
                                                                        @foreach($subject->teachers as $teacher)
                                                                            <li>
                                                                                <i class="fa fa-user-circle"></i> {{ $teacher->employee->name }}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p>No teachers assigned to this subject.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                <!-- /.modal -->
                            @endforeach
                        </div>
                        <!-- /.row -->
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
