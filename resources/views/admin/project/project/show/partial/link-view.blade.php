@extends('layouts.dashboard.app')

@section('title', 'Project Link Details')

@push('css')
<style>
    .card{
        background: rgba(255, 200, 200, 0.123);
        box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px;
   }
   table tr{
    border: 1px solid rgb(0, 0, 0);
   }
</style>

@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projects.index') }}">Project Link</a>
            </li>
            <li class="breadcrumb-item">
                Details
            </li>
        </ol>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center text-success ">Project Link Details</h3>
                    <table class="table table-bordered   table-hover table-info ">
                        <thead class="table-primary ">
                          <tr>
                            <th scope="col">Capnel Link</th>
                            <th scope="col">Password</th>
                            <th scope="col">Web Link</th>
                            <th scope="col">Git Link</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td >{{$projectLink->cpanel_link}}</td>
                            <td>{{$projectLink->cpanel_password	}}</td>
                            <td>{{$projectLink->web_link}}</td>
                            <td>{{$projectLink->git_link}}</td>
                          </tr>
                        </tbody>
                    </table>
                    <h3 class="text-center text-danger  mt-4">User List</h3>
                    <table class="table  table-bordered   table-hover ">
                        <thead class="table-warning ">
                          <tr>
                            <th scope="col">Role</th>
                            <th scope="col">Email</th>
                            <th scope="col">Password</th>
                          </tr>
                        </thead>
                        <tbody>
                            @if ($roles)
                                @foreach ($roles as $key=>$role)
                                  <tr>
                                    <td >{{$role}}</td>
                                    <td>{{$user_emails[$key]}}</td>
                                    <td><b>{{$user_password[$key]}}</b></td>
                                  </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <div class="mb-5"></div>


@endsection

