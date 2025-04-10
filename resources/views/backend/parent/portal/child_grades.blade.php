<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Child Grades @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            {{ $student->name }}'s Grades
            <small>View academic performance</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('parent.portal.dashboard')}}"><i class="fa fa-users"></i> Parent Portal</a></li>
            <li><a href="{{URL::route('parent.portal.child_details', $student->id)}}"><i class="fa fa-user"></i> Child Details</a></li>
            <li class="active">Grades</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Exam Results</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                @foreach($exams as $key => $exam)
                                    <li class="{{ $key == 0 ? 'active' : '' }}">
                                        <a href="#exam_{{ $exam->id }}" data-toggle="tab">{{ $exam->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($exams as $key => $exam)
                                    <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="exam_{{ $exam->id }}">
                                        @php
                                            $examResult = $results->where('exam_id', $exam->id)->first();
                                            $examMarks = $marks->where('exam_id', $exam->id);
                                        @endphp
                                        
                                        @if($examResult)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="box box-solid">
                                                        <div class="box-header with-border">
                                                            <h3 class="box-title">Result Summary</h3>
                                                        </div>
                                                        <div class="box-body">
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th>Total Marks</th>
                                                                    <td>{{ $examResult->total_marks }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>GPA</th>
                                                                    <td>{{ $examResult->grade_point }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Grade</th>
                                                                    <td>{{ $examResult->grade }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Point</th>
                                                                    <td>{{ $examResult->point }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Status</th>
                                                                    <td>
                                                                        @if($examResult->result_status == 1)
                                                                            <span class="label label-success">Passed</span>
                                                                        @else
                                                                            <span class="label label-danger">Failed</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="box box-solid">
                                                        <div class="box-header with-border">
                                                            <h3 class="box-title">Position Information</h3>
                                                        </div>
                                                        <div class="box-body">
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th>Class Position</th>
                                                                    <td>{{ $examResult->class_position }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Section Position</th>
                                                                    <td>{{ $examResult->section_position }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Class Highest</th>
                                                                    <td>{{ $examResult->class_highest_mark }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Class Average</th>
                                                                    <td>{{ $examResult->class_avg_mark }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="box box-solid">
                                                        <div class="box-header with-border">
                                                            <h3 class="box-title">Subject Marks</h3>
                                                        </div>
                                                        <div class="box-body table-responsive">
                                                            <table class="table table-bordered table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Subject</th>
                                                                        <th>Marks</th>
                                                                        <th>Grade</th>
                                                                        <th>Point</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($examMarks as $mark)
                                                                        <tr>
                                                                            <td>{{ $mark->subject->name }}</td>
                                                                            <td>{{ $mark->marks }}</td>
                                                                            <td>{{ $mark->grade }}</td>
                                                                            <td>{{ $mark->point }}</td>
                                                                            <td>
                                                                                @if($mark->is_pass)
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
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <h4><i class="icon fa fa-info"></i> Note!</h4>
                                                No results found for this exam.
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
        
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Academic Progress</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="chart">
                            <canvas id="progressChart" style="height: 300px;"></canvas>
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
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Create progress chart
            var ctx = document.getElementById('progressChart').getContext('2d');
            
            // Prepare data for chart
            var examNames = [];
            var examScores = [];
            
            @foreach($exams as $exam)
                @php
                    $examResult = $results->where('exam_id', $exam->id)->first();
                @endphp
                
                @if($examResult)
                    examNames.push('{{ $exam->name }}');
                    examScores.push({{ $examResult->total_marks }});
                @endif
            @endforeach
            
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: examNames,
                    datasets: [{
                        label: 'Total Marks',
                        data: examScores,
                        backgroundColor: 'rgba(60, 141, 188, 0.2)',
                        borderColor: 'rgba(60, 141, 188, 1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(60, 141, 188, 1)',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Academic Performance Trend'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
