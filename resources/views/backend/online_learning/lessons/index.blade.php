<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Online Learning - Lessons @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <h1>
            Lessons
            <small>{{ $course->name }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{URL::route('online_learning.courses')}}"><i class="fa fa-book"></i> Courses</a></li>
            <li><a href="{{URL::route('online_learning.courses.show', $course->id)}}"><i class="fa fa-eye"></i> Course Details</a></li>
            <li class="active">Lessons</li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">All Lessons for {{ $course->name }}</h3>
                        <div class="box-tools pull-right">
                            <a class="btn btn-info btn-sm" href="{{ route('online_learning.lessons.create', $course->id) }}"><i class="fa fa-plus-circle"></i> Add New</a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        @if($course->lessons->count() > 0)
                            <div class="table-responsive">
                                <table id="lessons-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">Order</th>
                                            <th width="30%">Title</th>
                                            <th width="10%">Duration</th>
                                            <th width="10%">Free</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Created</th>
                                            <th width="20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-lessons">
                                    @foreach($course->lessons->sortBy('order') as $lesson)
                                        <tr data-id="{{ $lesson->id }}">
                                            <td>
                                                <span class="handle">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </span>
                                                {{ $lesson->order }}
                                            </td>
                                            <td>{{ $lesson->title }}</td>
                                            <td>{{ $lesson->duration ? $lesson->duration.' min' : 'N/A' }}</td>
                                            <td>
                                                @if($lesson->is_free)
                                                    <span class="label label-success">Yes</span>
                                                @else
                                                    <span class="label label-default">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lesson->status == \App\Http\Helpers\AppHelper::ACTIVE)
                                                    <span class="label label-success">Published</span>
                                                @else
                                                    <span class="label label-warning">Draft</span>
                                                @endif
                                            </td>
                                            <td>{{ $lesson->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a title="View" href="{{ route('online_learning.lessons.show', [$course->id, $lesson->id]) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                                    <a title="Edit" href="{{ route('online_learning.lessons.edit', [$course->id, $lesson->id]) }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                                                    <a title="Delete" class="btn btn-danger btn-sm delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="{{ $lesson->id }}"><i class="fa fa-trash-o"></i></a>
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
                                No lessons created for this course yet. <a href="{{ route('online_learning.lessons.create', $course->id) }}">Create your first lesson</a>.
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
                    <h4 class="modal-title" id="deleteModalLabel">Delete Lesson</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this lesson?</p>
                    <p class="text-danger"><small>This action cannot be undone. All resources and student progress for this lesson will also be deleted.</small></p>
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
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize datatable
            $('#lessons-table').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false
            });
            
            // Handle delete button click
            $('.delete-btn').click(function () {
                var id = $(this).data('id');
                var url = "{{ route('online_learning.lessons.destroy', [$course->id, ':id']) }}";
                url = url.replace(':id', id);
                $('#delete-form').attr('action', url);
            });
            
            // Make lessons sortable
            $("#sortable-lessons").sortable({
                handle: '.handle',
                update: function(event, ui) {
                    var lessons = [];
                    $('#sortable-lessons tr').each(function() {
                        lessons.push($(this).data('id'));
                    });
                    
                    // Save the new order
                    $.ajax({
                        url: "{{ route('online_learning.lessons.reorder', $course->id) }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            lessons: lessons
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update the order numbers
                                $('#sortable-lessons tr').each(function(index) {
                                    $(this).find('td:first').text(index + 1);
                                });
                                
                                // Show success message
                                toastr.success('Lesson order updated successfully!');
                            }
                        },
                        error: function() {
                            toastr.error('An error occurred while updating lesson order.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
