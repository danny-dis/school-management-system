@extends('layouts.master')

@section('title', 'Module Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Module Management</h3>
                <div class="card-tools">
                    <form action="{{ route('admin.modules.refresh-cache') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-refresh"></i> Refresh Cache
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Licensed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)
                                <tr>
                                    <td>
                                        <i class="fa {{ $module->icon }}"></i>
                                        {{ $module->name }}
                                    </td>
                                    <td>{{ $module->description }}</td>
                                    <td>
                                        @if($module->status)
                                            <span class="badge badge-success">Enabled</span>
                                        @else
                                            <span class="badge badge-danger">Disabled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array($module->module_key, $licensedModules))
                                            <span class="badge badge-success">Licensed</span>
                                        @else
                                            <span class="badge badge-warning">Not Licensed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($module->status)
                                            <form action="{{ route('admin.modules.disable', $module->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to disable this module?')">
                                                    <i class="fa fa-power-off"></i> Disable
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.modules.enable', $module->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" {{ !in_array($module->module_key, $licensedModules) ? 'disabled' : '' }}>
                                                    <i class="fa fa-power-off"></i> Enable
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
        </div>
    </div>
</div>
@endsection
