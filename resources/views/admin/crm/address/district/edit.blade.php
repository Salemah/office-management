@extends('layouts.dashboard.app')

@section('title', 'CRM')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>

            </li>
            <li class="breadcrumb-item">
                <span>District</span>
            </li>
        </ol>
        <a href="" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.crm.district.update',$district->id) }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <p class="m-0">Edit District</p>
                        <a class="btn btn-danger text-white" href="{{ redirect()->getUrlGenerator()->previous() }}">
                            <i class='bx bx-chevron-left'></i> Back
                        </a>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="name">District Name<span class="text-danger">*</span></label>
                                <input type="text" name="districts_name" id="districts_name" class="form-control @error('districts_name') is-invalid @enderror" value="{{ old('districts_name',$district->districts_name) }}" placeholder="District Name">
                                @error('districts_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="status">Country<span class="text-danger">*</span></label>
                                <select name="country_id" id="country_id"
                                        class="form-select @error('country_id') is-invalid @enderror">
                                    <option value="{{ $district->country->id }}" selected>{{ $district->country->country_name }}</option>
                                    <hr>
                                    @foreach($countrys as $country)
                                        @if($district->country->id !== $country->id)
                                            <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                        @endif

                                    @endforeach
                                </select>
                                @error('country_id')
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

