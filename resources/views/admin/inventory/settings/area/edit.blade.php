@extends('layouts.dashboard.app')

@section('title', 'Edit Area')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>

            </li>
            <li class="breadcrumb-item">
             <a href="{{route('admin.inventory.settings.area.index')}}">Area</a>
            </li>
        </ol>
        <a href="{{ route('admin.inventory.settings.area.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.settings.area.update', $data->id )}}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                                <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                    <label for="name"><b>Area Name</b><span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ $data->name }}"
                                        placeholder="Enter area name">
                                    @error('name')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                    <label for="area_code"><b>Area Code</b><span class="text-danger">*</span></label>
                                    <input type="text" name="area_code" id="area_code"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ $data->area_code }}"
                                        placeholder="Enter Unit Code">
                                    @error('area_code')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                    <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                    <select name="status" id="status"
                                            class="form-select @error('status') is-invalid @enderror">
                                        <option >--Select Status--</option>
                                        <option value="1" {{ $data->status== 1 ? 'selected': '' }}>Active</option>
                                        <option value="0" {{ $data->status== 0 ? 'selected': '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                    <span class="alert text-danger" role="alert">
                                         <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                        <div class="row">
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <div class="mb-5"></div>

@endsection
