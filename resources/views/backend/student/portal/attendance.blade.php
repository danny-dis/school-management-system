<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Student Attendance @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Attendance Records
            <small>View your attendance history</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('student.portal.dashboard')}}"><i class="fa fa-user"></i> Student Portal</a></li>
            <li class="active">Attendance</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
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
                        <!-- /.row -->
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart">
                                    <canvas id="attendanceChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Attendance Records</h3>
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
                                @foreach($attendances as $attendance)
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
                    <div class="box-footer clearfix">
                        {{ $attendances->links() }}
                    </div>
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
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Create attendance chart
            var ctx = document.getElementById('attendanceChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{
                        data: [{{ $presentDays }}, {{ $absentDays }}],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Attendance Distribution'
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
