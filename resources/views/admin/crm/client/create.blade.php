@extends('layouts.dashboard.app')

@section('title', 'Client ')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            padding: 6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }
    </style>

@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });
    </script>
@endpush
@section('breadcrumb')

    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <span>Client </span>
            </li>
        </ol>
        <div>
            {{-- <p class="text-warning">Client default password is "client". </p> --}}
        </div>
        <a href="{{ route('admin.crm.client.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.crm.client.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="client_name"><b>Name</b><span class="text-danger">*</span></label>
                                <input type="text" name="client_name" id="client_name"class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name') }}" placeholder="Enter Name">
                                @error('client_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                                <select name="status" id="status"class="form-select @error('status') is-invalid @enderror">
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
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="primary_phone"><b>Primary Phone</b><span class="text-danger">*</span></label>
                                <input type="text" name="primary_phone" id="primary_phone" class="form-control @error('primary_phone') is-invalid @enderror" value="{{ old('primary_phone') }}"  placeholder="Enter Primary Phone">
                                @error('primary_phone')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="client_type"><b>Client Type</b><span class="text-danger">*</span></label>
                                <select name="client_type" id="client_type"class="form-select @error('client_type') is-invalid @enderror">
                                    <option selected>--Select Client Type--</option>
                                    @foreach ($ClientTypes as $clienttype )
                                        <option value="{{$clienttype->id}}">{{$clienttype->name}}</option>
                                    @endforeach
                                </select>
                                @error('client_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="client_type_priority"><b>Client Priority</b><span class="text-danger">*</span></label>
                                <select name="client_type_priority" id="client_type_priority"
                                        class="form-select ">
                                    <option>--Select Priority--</option>
                                    @foreach ($priorities as $key => $priority )
                                        <option value="{{$priority->id}}">{{$priority->name}}</option>
                                    @endforeach
                                </select>
                                @error('client_type_priority')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="contact_through"><b>Data Source</b><span class="text-danger">*</span></label>
                                <select name="contact_through" id="contact_through" class="form-select @error('contact_through') is-invalid @enderror">
                                    <option selected>--Select Contact Through--</option>
                                    @foreach ($ContactThrough as $contact_through )
                                        <option value="{{$contact_through->id}}">{{$contact_through->name}}</option>
                                    @endforeach
                                </select>
                                @error('contact_through')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="interested_on"><b>Interested On</b></label>
                                <select name="interested_on" id="interested_on" class="form-select @error('interested_on') is-invalid @enderror">
                                    <option selected>--Select Contact Through--</option>
                                    @foreach ($InterestedsOn as $InterestedOn )
                                        <option value="{{$InterestedOn->id}}">{{$InterestedOn->name}}</option>
                                    @endforeach
                                </select>
                                @error('interested_on')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="client_business_category_id"><b>Client Business Category</b><span class="text-danger">*</span></label>
                                <select name="client_business_category_id" id="client_business_category_id" class="form-select @error('client_business_category_id') is-invalid @enderror">
                                    <option selected value="">--Client Business Category--</option>
                                    @foreach ($businessCategories as $businessCategory )
                                        <option value="{{$businessCategory->id}}">{{$businessCategory->name}}</option>
                                    @endforeach
                                </select>
                                @error('client_business_category_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="email"><b>Email</b></label>
                                <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter Email">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-12  my-3">
                                <label for="present_address"><b>Office Address</b></label>
                                <textarea name="present_address" id="present_address" cols="66" rows="4" placeholder="Enter Present Address"
                                          class="form-control @error('present_address') is-invalid @enderror"></textarea>
                                @error('present_address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="country"><b>Country</b></label>
                                <select name="country" id="country" class="form-control select2">
                                    <option value="">--Select Country--</option>
                                </select>
                                @error('country')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="states"><b>State</b></label>
                                <select name="states" id="states"
                                        class="form-control select2">
                                    <option value="">--Select State--</option>
                                </select>
                                @error('states')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-4">
                                <label for="cities"><b>City</b></label>
                                <select name="cities" id="cities" class="form-control select2">
                                    <option value="">--Select City--</option>
                                </select>
                                @error('cities')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-4">
                                <label for="zip"><b>Zip</b></label>
                                <input type="text" placeholder="Enter Zip Code" class="form-control" name="zip" id="zip"
                                       value="{{old('zip')}}">
                                @error('zip')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                                <div class="form-check my-3">
                                    <input class="form-check-input" type="checkbox" value="" id="addressCheckChecked" >
                                    <label class="form-check-label" for="addressCheckChecked" >
                                      Other Address
                                    </label>
                                  </div>
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-12 mb-2 others"  style="display: none" >
                                <label for="permanent_address"><b>Other Address</b></label>
                                <textarea name="permanent_address" id="permanent_address_other" cols="66" rows="4"
                                          placeholder="Enter Permanent Address"
                                          class="form-control @error('permanent_address') is-invalid @enderror">{{old('permanent_address')}}</textarea>
                                @error('permanent_address_other')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 others"  style="display: none"   >
                                <label for="country_other"><b>Country</b><span class="text-danger">*</span></label>
                                <select name="country_other" id="country_other" class="form-control select2">
                                    <option value="">--Select Country--</option>
                                </select>

                                @error('country_other')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 others"   style="display: none" >
                                <label for="states_other"><b>State</b><span class="text-danger">*</span></label>
                                <select name="states_other" id="states_other"
                                        class="form-control select2">
                                    <option value="">--Select State--</option>

                                </select>
                                @error('states_other')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 others"  style="display: none" >
                                <label for="cities_other"><b>City</b><span class="text-danger">*</span></label>
                                <select name="cities_other" id="cities_other" class="form-control select2">
                                    <option value="">--Select City--</option>
                                </select>
                                @error('cities_other')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 others"  style="display: none" >
                                <label for="zip_other"><b>Zip</b><span class="text-danger">*</span></label>
                                <input type="text" name="zip_other" id="zip_other" placeholder="Enter Zip Code" class="form-control"
                                       value="">
                                @error('zip_other')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="10" cols="40"class="form-control @error('description') is-invalid @enderror"value="{{ old('description') }}"placeholder="Description..."></textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <div class="mb-5"></div>

@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('#client_business_category_id').select2();
        $('#country').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.country.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function (data) {
                    console.log();
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });

        $('#states').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.state.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        country_id: $('#country').val()
                    }
                    return query;
                },
                processResults: function (data) {
                    console.log();
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });

        $('#cities').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.city.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        state_id: $('#states').val()
                    }
                    return query;
                },
                processResults: function (data) {
                    console.log();
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });
        $('#country_other').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.country.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function (data) {
                    console.log();
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });

        $('#states_other').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.state.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        country_id: $('#country_other').val()
                    }
                    return query;
                },
                processResults: function (data) {
                    console.log();
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });

        $('#cities_other').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.city.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        state_id: $('#states_other').val()
                    }
                    return query;
                },
                processResults: function (data) {
                    console.log();
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });

        $(document).on("click", "#addressCheckChecked", function () {
            if ($('#addressCheckChecked').is(":checked"))
                $(".others").show();
            else
                $(".others").hide();
                $("#permanent_address_other").val('');
                $("#country_other").val(null).trigger('change');
                $("#states_other").val(null).trigger('change');
                $("#cities_other").val(null).trigger('change');
                $("#zip_other").val('');


        });
        CKEDITOR.replace('description', {
            toolbarGroups: [
                {"name": "styles", "groups": ["styles"]},
                {"name": "basicstyles", "groups": ["basicstyles"]},
                {"name": "paragraph", "groups": ["list", "blocks"]},
                {"name": "document", "groups": ["mode"]},
                {"name": "links", "groups": ["links"]},
                {"name": "insert", "groups": ["insert"]},
                {"name": "undo", "groups": ["undo"]},
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

        // client_typeData
        $(document).on('change', '#client_type', function () {
            var id = $("#client_type").val();
            var url = '{{ route('admin.crm.client.type.priority', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    $('#client_type_priority').val(resp.priority);
                },
                error: function () {
                    location.reload();
                }
            });
        });
    </script>
@endpush
