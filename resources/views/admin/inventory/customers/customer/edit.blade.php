@extends('layouts.dashboard.app')

@section('title', 'Edit Customer')
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-selection {
        height: 38px !important;
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
                <span>Customer </span>
            </li>
        </ol>
        <a href="{{ route('admin.inventory.customers.customer.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')
@include('layouts.dashboard.partials.alert')
<form action="{{ route('admin.inventory.customers.customer.update', $customer->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="warehouse"><b>Warehouse</b><span class="text-danger">*</span></label>
                            <select class="form-control warehouse " id="warehouse" name="warehouse" required onchange="getEmployees()">
                                <option value="" selected>--Select Warehouse--</option>
                                @foreach($warehouses as $warehouse)
                                     <option value="{{$warehouse->id}}" @if($warehouse->id == $customer->warehouse_id) {{'selected'}} @endif>{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                            @error('warehouse')
                                <span class="text-danger" role="alert">
                                   <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="name"><b>Name</b><span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ $customer->name }}" placeholder="Enter Name">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="customer_type_priority"><b>Customer Priority</b><span class="text-danger">*</span></label>
                            <select name="customer_type_priority" id="customer_type_priority"
                                class="form-select select2 select">
                                <option >--Select Priority--</option>
                                <option value="1" {{ $customer->customer_type_priority == 1 ? 'selected' : '' }}>First</option>
                                <option value="2" {{ $customer->customer_type_priority == 2 ? 'selected' : '' }}>Second</option>
                                <option value="3" {{ $customer->customer_type_priority == 3 ? 'selected' : '' }}>Third</option>
                            </select>
                            @error('customer_type_priority')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="email"><b>Email</b></label>
                            <input type="email" name="email" id="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ $customer->email }}"
                                placeholder="Enter email address">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="phone"><b>Phone</b><span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ $customer->phone }}" placeholder="Enter phone number">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="tax_number"><b>Tax Number</b></label>
                            <input type="text" name="tax_number" id="tax_number"
                                class="form-control"
                                value="{{ $customer->tax_number }}" placeholder="Enter tax number">

                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <div class="form-group">
                                <label for="country_id"><b>Country</b></label>
                                <select name="country_id" id="country_id" class="form-control select2 select">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $key => $country)
                                        <option value="{{ $country->id }}" @if ($country->id == $customer->country_id) selected
                                        @endif> {{ $country->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <div class="form-group">
                                <label for="state_id"><b>State</b></label>
                                <select name="state_id" id="state_id" class="form-control select2 select">
                                    <option value="">Select State</option>
                                    @foreach ($states as $key => $state)
                                        <option value="{{ $state->id }}" @if ($state->id == $customer->state_id) selected @endif> {{ $state->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <div class="form-group">
                                <label for="city_id"><b>City</b></label>
                                <select name="city_id" id="city_id" class="form-control select2 select">
                                    <option value="">Select City</option>
                                    @foreach ($cities as $key => $city)
                                        <option value="{{ $city->id }}" @if ($city->id == $customer->city_id) selected @endif> {{ $city->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="postal_code"><b>Postal Code</b></label>
                            <input type="number" name="postal_code" id="postal_code"
                                class="form-control" value="{{ $customer->postal_code }}" placeholder="Enter postal code">
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="contact_person"><b>Contact Person</b></label>
                            <input type="text" name="contact_person" id="contact_person"
                                class="form-control"
                                value="{{ $customer->contact_person }}" placeholder="Enter contact person">
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="area_id"><b> Area</b></label>
                            <select name="area_id" id="area_id" class="form-control select2 select">
                                <option>--Select Area--</option>
                                @foreach ($areas as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $customer->area_id) selected @endif>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ $customer->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $customer->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="form-group col-6 mb-2">
                            <label for="address"><b>Address</b><span class="text-danger">*</span></label>
                            <textarea name="address" id="address" rows="2" cols="3"
                                class="form-control @error('address') is-invalid @enderror"
                                placeholder="address...">{{ $customer->address }}</textarea>
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-6 mb-2">
                            <label for="description"><b>Description</b></label>
                            <textarea name="description" id="description" rows="2" cols="3"
                                class="form-control" placeholder="Description...">{{ $customer->description }}</textarea>
                        </div>
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
@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script>
        $(".select2").select2();
        $("#country_id").on('change', function(){
           var country_id = $('#country_id').val();
           $.ajax({
               url: "{{ route('admin.inventory.customers.country-wise-state') }}",
               type: "GET",
               data: {
                   'country_id':country_id,
               },
               success: function(data){

                   $('#state_id').empty();
                   $('#state_id').append("<option value=''>---Select State---</option>");

                   $.each(data, function(key, state){
                       $('#state_id').append("<option value="+state.id+">"+state.name+"</option>");
                   });
               },
           });
       });

       $("#state_id").on('change', function(){
           var state_id = $('#state_id').val();
           $.ajax({
               url: "{{ route('admin.inventory.customers.state-wise-city') }}",
               type: "GET",
               data: {
                   'state_id':state_id,
               },
               success: function(data){

                   $('#city_id').empty();
                   $('#city_id').append("<option value=''>---Select City---</option>");

                   $.each(data, function(key, city){
                       $('#city_id').append("<option value="+city.id+">"+city.name+"</option>");
                   });
               },
           });
       });
    </script>
@endpush
