@extends('layouts.dashboard.app')

@section('title', 'CRM')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>

            </li>
            <li class="breadcrumb-item">
                <span>State</span>
            </li>
        </ol>
        <a href="" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.address.state.update',$state->id) }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <p class="m-0">Update State</p>
                        <a class="btn btn-danger text-white" href="{{ redirect()->getUrlGenerator()->previous() }}">
                            <i class='bx bx-chevron-left'></i> Back
                        </a>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="name">State Name<span class="text-danger">*</span></label>
                                <input type="text" name="state_name" id="state_name"
                                       class="form-control @error('state_name') is-invalid @enderror"
                                       value="{{ old('state_name',$state->name) }}" placeholder="State Name">
                                @error('state_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
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

