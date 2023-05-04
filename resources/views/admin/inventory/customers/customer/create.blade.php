@extends('layouts.dashboard.app')

@section('title', 'Create Customer')
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<style>
    .checkBox{
        height: 15px;
        width: 15px;
    }
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
             <a href="{{route('admin.inventory.customers.customer.index')}}">Customer</a>
            </li>
        </ol>
        <a href="{{ route('admin.inventory.customers.customer.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')
    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.inventory.customers.customer.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-4 mt-4">
                                <div class="form-group">
                                    <input type="checkbox" class="checkBox" name="both" value="1">&nbsp;
                                    <label>Both Customer and Supplier</label>
                                </div>
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="warehouse"><b>Warehouse</b><span class="text-danger">*</span></label>
                                <select class="form-control warehouse " id="warehouse" name="warehouse" required onchange="getEmployees()">

                                    @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                                        <option value="" selected>--Select Warehouse--</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endforeach
                                    @elseif($user_role->name == 'Employee')
                                        {{$auth = Auth::user()}}
                                        {{$emp_ware=\App\Models\Employee\Employee::where('id',$auth->user_id)->with('ware_house')->first()}}
                                        <option value="{{$emp_ware->warehouse}} readonly">{{$emp_ware->ware_house->name}}</option>
                                    @endif
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
                                    value="{{ old('name') }}" placeholder="Enter Name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2" id="company">
                                <label for="company_name"><b>Company Name</b><span id="companyRequired" class="text-danger">*</span></label>
                                <input type="text" name="company_name" id="company_name"
                                    class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="Enter company name">
                                @error('company_name')
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
                                    <option value="1">First</option>
                                    <option value="2">Second</option>
                                    <option value="3">Third</option>
                                </select>
                                @error('customer_type_priority')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="email"><b>Email</b><span id="emailRequired" class="text-danger">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
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
                                    value="{{ old('phone') }}" placeholder="Enter phone number">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="tax_number"><b> Tax Number</b></label>
                                <input type="text" name="tax_number" id="tax_number"
                                    class="form-control"
                                    value="{{ old('tax_number') }}" placeholder="Enter tax number">

                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="country_id"><b>Country</b></label>

                                    <select name="country_id" id="country_id" class="form-control select2 select">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $key => $country)
                                            <option value="{{ $country->id }}" > {{ $country->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="state_id"><b>State</b></label>
                                    <select name="state_id" id="state_id" class="form-control select2 select">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <div class="form-group">
                                    <label for="city_id"><b>City</b></label>
                                    <select name="city_id" id="city_id" class="form-control select2 select">
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="postal_code"><b> Postal Code</b></label>
                                <input type="number" name="postal_code" id="postal_code"
                                    class="form-control" value="{{ old('postal_code') }}" placeholder="Enter postal code">
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="contact_person"><b>Contact Person</b></label>
                                <input type="text" name="contact_person" id="contact_person"
                                    class="form-control"
                                    value="{{ old('contact_person') }}" placeholder="Enter contact person">
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="area_id"><b> Area</b></label>
                                <select name="area_id" id="area_id" class="form-control select2 select">
                                    <option>--Select Area--</option>
                                    @foreach ($areas as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                <select name="status" id="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option>--Select Status--</option>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
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
                                    class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}"
                                    placeholder="address..."></textarea>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-6 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="2" cols="3"
                                    class="form-control" value="{{ old('description') }}"
                                    placeholder="Description..."></textarea>
                            </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
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
        // customer_typeData
        $(document).on('change', '#customer_type', function () {
            var id = $("#customer_type").val();
            var url = '{{ route('admin.inventory.customers.customer.type.priority', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function(resp) {
                    $('#customer_type_priority').val(resp.priority);
                },
                error: function() {
                    location.reload();
                }
            });
        });

        $('#emailRequired').hide();
        $('#companyRequired').hide();
        $('#company').hide();

        $(".checkBox").click(function(){
            if($(this).is(":checked")) {
                $('#emailRequired').show();
                $('#companyRequired').show();
                $('#company').show();
            }else{
                $('#emailRequired').hide();
                $('#companyRequired').hide();
                $('#company').hide();
            }
        });

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
