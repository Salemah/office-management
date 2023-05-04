@extends('layouts.dashboard.app')

@section('title', 'Edit Tax')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
        integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .dropify-wrapper .dropify-message p {
            font-size: initial;

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
             <a href="{{route('admin.inventory.settings.tax.index')}}">Tax</a>
            </li>
        </ol>
        <a href="{{ route('admin.inventory.settings.tax.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.settings.tax.update', $tax->id )}}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="name"><b>Name</b><span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"class="form-control @error('name') is-invalid @enderror"value="{{$tax->name}}"placeholder="Enter Warehouse Name">
                                    @error('name')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="rate"><b>Rate</b><span class="text-danger">*</span></label>
                                    <input type="number" name="rate" id="rate"class="form-control @error('rate') is-invalid @enderror"value="{{$tax->rate}}"placeholder="Enter Tax Rate">
                                    @error('rate')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                    <select name="status" id="status"class="form-select @error('status') is-invalid @enderror">
                                        <option >--Select Status--</option>
                                        <option value="1"{{$tax->status == 1 ? 'selected': ''}}>Active</option>
                                        <option value="0" {{$tax->status == 0 ? 'selected': ''}}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <span class="alert text-danger" role="alert">
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
    </form>
    <div class="mb-5"></div>


@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
        integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script>
        $(document).ready(function() {
            $('.dropify').dropify();
        });


    </script>
@endpush
