@extends('layouts.master')

@section('title', 'Notifications')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Notifications</h3>
                <div class="card-tools">
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-check-circle"></i> Mark All as Read
                        </button>
                    </form>
                    <form action="{{ route('notifications.destroy-all') }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete all notifications?')">
                            <i class="fa fa-trash"></i> Delete All
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if(count($notifications) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                    <tr class="{{ $notification->read ? '' : 'table-info' }}">
                                        <td>{{ $notification->title }}</td>
                                        <td>{{ $notification->message }}</td>
                                        <td>
                                            @if($notification->type == 'info')
                                                <span class="badge badge-info">Info</span>
                                            @elseif($notification->type == 'success')
                                                <span class="badge badge-success">Success</span>
                                            @elseif($notification->type == 'warning')
                                                <span class="badge badge-warning">Warning</span>
                                            @elseif($notification->type == 'error')
                                                <span class="badge badge-danger">Error</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                                        <td>
                                            @if($notification->read)
                                                <span class="badge badge-success">Read</span>
                                            @else
                                                <span class="badge badge-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$notification->read)
                                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-check"></i> Mark as Read
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($notification->link)
                                                <a href="{{ $notification->link }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-link"></i> View
                                                </a>
                                            @endif
                                            
                                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this notification?')">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> You have no notifications.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
