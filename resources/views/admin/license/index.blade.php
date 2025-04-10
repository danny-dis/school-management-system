@extends('layouts.master')

@section('title', 'License Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">License Management</h3>
            </div>
            <div class="card-body">
                @if($licenseDetails['valid'])
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> Your license is valid and active.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> Your license is not valid or has expired.
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">License Details</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Customer Name</th>
                                        <td>{{ $licenseDetails['customer_name'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer Email</th>
                                        <td>{{ $licenseDetails['customer_email'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>License Key</th>
                                        <td>
                                            @if($licenseDetails['license_key'])
                                                {{ substr($licenseDetails['license_key'], 0, 8) }}...{{ substr($licenseDetails['license_key'], -8) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>License Status</th>
                                        <td>
                                            @if($licenseDetails['valid'])
                                                <span class="badge badge-success">Valid</span>
                                            @else
                                                <span class="badge badge-danger">Invalid</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Expires On</th>
                                        <td>
                                            @if($licenseDetails['expires_at'])
                                                {{ date('F j, Y', strtotime($licenseDetails['expires_at'])) }}
                                                @if($daysUntilExpiration > 0)
                                                    <span class="badge badge-info">{{ $daysUntilExpiration }} days left</span>
                                                @else
                                                    <span class="badge badge-danger">Expired</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Support Expires On</th>
                                        <td>
                                            @if($licenseDetails['support_expires_at'])
                                                {{ date('F j, Y', strtotime($licenseDetails['support_expires_at'])) }}
                                                @if($daysUntilSupportExpiration > 0)
                                                    <span class="badge badge-info">{{ $daysUntilSupportExpiration }} days left</span>
                                                @else
                                                    <span class="badge badge-danger">Expired</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Max Students</th>
                                        <td>{{ $licenseDetails['max_students'] ?? 'Unlimited' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Max Teachers</th>
                                        <td>{{ $licenseDetails['max_teachers'] ?? 'Unlimited' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Max Employees</th>
                                        <td>{{ $licenseDetails['max_employees'] ?? 'Unlimited' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Licensed Modules</h4>
                            </div>
                            <div class="card-body">
                                @if(count($licensedModules) > 0)
                                    <ul class="list-group">
                                        @foreach($licensedModules as $module)
                                            <li class="list-group-item">
                                                <i class="fa fa-check-circle text-success"></i>
                                                {{ ucwords(str_replace('_', ' ', $module)) }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="alert alert-info">
                                        No modules are licensed.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Validate License</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.license.validate') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="license_key">License Key</label>
                                        <input type="text" class="form-control @error('license_key') is-invalid @enderror" id="license_key" name="license_key" placeholder="Enter your license key" value="{{ old('license_key') }}">
                                        @error('license_key')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">Validate License</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">License Management</h4>
                            </div>
                            <div class="card-body">
                                <p>If you need to update your license information, you can clear the license cache and validate your license again.</p>
                                <form action="{{ route('admin.license.clear-cache') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">Clear License Cache</button>
                                </form>
                                
                                <hr>
                                
                                <p>Need to purchase a license or upgrade your existing one?</p>
                                <a href="https://zophlic.com/pricing" target="_blank" class="btn btn-success">Purchase License</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
