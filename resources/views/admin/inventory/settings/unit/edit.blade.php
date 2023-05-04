@extends('layouts.dashboard.app')

@section('title', 'Edit Unit')

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
             <a href="{{route('admin.inventory.settings.unit.index')}}">Unit</a>
            </li>
        </ol>
        <a href="{{ route('admin.inventory.settings.unit.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.settings.unit.update', $unit->id )}}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="name"><b>Unit Name</b><span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ $unit->name }}"
                                        placeholder="Enter Unit Name">
                                    @error('name')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="unit_code"><b>Unit Code</b><span class="text-danger">*</span></label>
                                    <input type="text" name="unit_code" id="unit_code"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ $unit->unit_code }}"
                                        placeholder="Enter Unit Code">
                                    @error('unit_code')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="base_unit"><b>Select Base Unit</b><span class="text-danger">*</span></label>
                                    <select name="base_unit" id="base_unit" onchange="checkBaseUnit()" class="form-select @error('base_unit') is-invalid @enderror">
                                        <option value="">--Select Base Unit--</option>
                                        @foreach($units as $item)
                                            <option value="{{ $item->id }}"
                                                @if ($baseUnit)
                                                    @if ($item->id == $baseUnit->id)
                                                    {{ 'selected' }}
                                                    @endif
                                                @endif >
                                                {{ $item->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('base_unit')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2" @if(isset($baseUnit)) @else style="display: none" @endif id="operators" >
                                    <label for="operator"><b>Operator</b><span class="text-danger">*</span></label>
                                    <input type="text" name="operator" id="operator"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{$unit->operator}}"
                                        placeholder="Enter Operator Value">
                                    @error('operator')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2" @if(isset($baseUnit)) @else style="display: none" @endif id="operations">
                                    <label for="operation_value"><b>Operation Value</b><span class="text-danger">*</span></label>
                                    <input type="text" name="operation_value" id="operation_value"class="form-control @error('operation_value') is-invalid @enderror"value="{{ $unit->operation_value }}"placeholder="Enter Operation Value">
                                    @error('operation_value')
                                        <span class="alert text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                    <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                    <select name="status" id="status"
                                            class="form-select @error('status') is-invalid @enderror">
                                        <option >--Select Status--</option>
                                        <option value="1" {{ $unit->status== 1 ? 'selected': '' }}>Active</option>
                                        <option value="0" {{ $unit->status== 0 ? 'selected': '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="form-group col-12 mb-2">
                                    <label for="description"><b>Description</b></label>
                                    <textarea name="description" id="description" rows="3"
                                        class="form-control @error('description') is-invalid @enderror"
                                        value="{{ old('description') }}"
                                        placeholder="Description...">{{ $unit->description }}</textarea>
                                    @error('description')
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

    <script>
        function checkBaseUnit(){
               if($('#base_unit').val() != 0)
                {
                     $('#operations').show();
                     $('#operators').show();
                }
                else{
                    $('#operations').hide();
                    $('#operators').hide();
                    $('#operation_value').val('');
                    $('#operator').val('');
                }
       }

    </script>
@endpush
