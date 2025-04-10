<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Online Learning - Assignments @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Assignments
            <small>{{ $course->name }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('online_learning.courses')}}"><i class="fa fa-book"></i> Courses</a></li>
            <li><a href="{{URL::route('online_learning.courses.show', $course->id)}}"><i class="fa fa-eye"></i> Course Details</a></li>
            <li class="active">Assignments</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">All Assignments for {{ $course->name }}</h3>
                        <div class="box-tools pull-right">
                            <a class="btn btn-info btn-sm" href="{{ route('online_learning.assignments.create', $course->id) }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        @if($course->assignments->count() > 0)
                            <div class="table-responsive">
                                <table id="assignments-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="25%">Title</th>
                                            <th width="15%">Due Date</th>
                                            <th width="10%">Total Marks</th>
                                            <th width="10%">Submissions</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Created</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($course->assignments as $assignment)
                                        <tr>
                                            <td>{{ $assignment->title }}</td>
                                            <td>{{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y H:i') }}</td>
                                            <td>{{ $assignment->total_marks }}</td>
                                            <td>{{ $assignment->submissions->count() }}</td>
                                            <td>
                                                @if($assignment->status == \App\Http\Helpers\AppHelper::ACTIVE)
                                                    <span class="label label-success">Active</span>
                                                @else
                                                    <span class="label label-warning">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a title="View" href="{{ route('online_learning.assignments.show', [$course->id, $assignment->id]) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                                    <a title="Edit" href="{{ route('online_learning.assignments.edit', [$course->id, $assignment->id]) }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                                                    <a title="Delete" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="{{ $assignment->id }}"><i class="fa fa-trash-o"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h4><i class="icon fa fa-info"></i> Note!</h4>
                                No assignments created for this course yet. <a href="{{ route('online_learning.assignments.create', $course->id) }}">Create your first assignment</a>.
                            </div>
                        @endif
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deleteModalLabel">Delete Assignment</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this assignment?</p>
                    <p class="text-danger"><small>This action cannot be undone. All submissions for this assignment will also be deleted.</small></p>
                </div>
                <div class="modal-footer">
                    <form id="delete-form" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize datatable
            $('#assignments-table').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false
            });
            
            // Handle delete button click
            $('.delete-btn').click(function () {
                var id = $(this).data('id');
                var url = "{{ route('online_learning.assignments.destroy', [$course->id, ':id']) }}";
                url = url.replace(':id', id);
                $('#delete-form').attr('action', url);
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
