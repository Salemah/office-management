@extends('layouts.dashboard.app')

@section('title', 'Role Permission')

@push('css')
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{ route('admin.settings.role.index') }}">Role Permission</a>
            </li>

        </ol>
        <a class="btn btn-sm btn-dark" href="{{ route('admin.settings.role.index') }}">
            Back to list
        </a>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="form-group mb-2">
                        <label for="role">Role name</label>
                        <input type="text" class="form-control is-valid" id="role" name="name"
                            value="{{ $role->name }}" placeholder="Insert Role">
                        <input type="hidden" name="id" value="{{ $role->id }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-12 mb-2">
                        <div class="form-group">
                            <label for="description"><b>Description</b></label>
                            <textarea name="description" class="form-control " id="description" cols="100" rows="3"
                                placeholder="Enter Role Details">{{ $role->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-4 mt-2">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Permission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($role->permissions as $rPermission)
                                    @foreach ($permissions as $permission)
                                        <tr>
                                            @if ($rPermission->id == $permission->id)
                                                <td style="color: rgb(46, 97, 168)">
                                                    <b>{{ $permission->name }}</b>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $('#all_permission_checkbox').on('click', function() {
            if ($(this).is(':checked')) {
                $('input[type=checkbox]').prop('checked', true);
            } else {
                $('input[type=checkbox]').prop('checked', false);
            }
        });
    </script>
@endpush
