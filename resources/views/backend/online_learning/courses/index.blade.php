<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Online Learning - Courses @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Courses
            <small>List of all courses</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Courses</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">All Courses</h3>
                        <div class="box-tools pull-right">
                            <a class="btn btn-info btn-sm" href="{{ route('online_learning.courses.create') }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="courses-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Teacher</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($courses as $course)
                                    <tr>
                                        <td>{{ $course->code }}</td>
                                        <td>{{ $course->name }}</td>
                                        <td>{{ $course->class->name }}</td>
                                        <td>{{ $course->teacher->name }}</td>
                                        <td>
                                            @if($course->status == \App\Http\Helpers\AppHelper::ACTIVE)
                                                <span class="label label-success">Active</span>
                                            @else
                                                <span class="label label-warning">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a title="View" href="{{ route('online_learning.courses.show', $course->id) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                                <a title="Edit" href="{{ route('online_learning.courses.edit', $course->id) }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                                                <a title="Lessons" href="{{ route('online_learning.lessons.index', $course->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-book"></i></a>
                                                <a title="Assignments" href="{{ route('online_learning.assignments.index', $course->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-tasks"></i></a>
                                                <a title="Manage Students" href="{{ route('online_learning.courses.students', $course->id) }}" class="btn btn-default btn-sm"><i class="fa fa-users"></i></a>
                                                <a title="Delete" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="{{ $course->id }}"><i class="fa fa-trash-o"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="text-center">
                            {{ $courses->links() }}
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deleteModalLabel">Delete Course</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this course?</p>
                    <p class="text-danger"><small>This action cannot be undone. All lessons, assignments, and student enrollments for this course will also be deleted.</small></p>
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
            $('#courses-table').DataTable({
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
                var url = "{{ route('online_learning.courses.destroy', ':id') }}";
                url = url.replace(':id', id);
                $('#delete-form').attr('action', url);
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
