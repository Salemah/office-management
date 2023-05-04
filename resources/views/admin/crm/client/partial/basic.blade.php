@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
          integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>


    <style>
        .dropify-wrapper .dropify-message p {
            font-size: initial;
        }

        .dropify-wrapper {
            height: 180px;
            width: 180px;
        }
    </style>
@endpush

<form action="{{ route('admin.crm.client.update',$Client->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <input type="file" id="image" class="dropify form-control @error('image') is-invalid @enderror"
                   @if ($Client->image) data-default-file="{{ asset('img/client/' . $Client->image) }}"
                   @else
                       data-default-file="{{asset('img/no-image/noman.jpg')}}"
                   @endif name="image">
            @error('image')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="client_name"> <b>Name</b><span class="text-danger">*</span></label>
            <input type="text" name="client_name" id="client_name"
                   class="form-control @error('client_name') is-invalid @enderror" value="{{$Client->name}}"
                   placeholder="Enter Name">
            @error('client_name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="primary_phone"><b> Primary Phone </b><span class="text-danger">*</span></label>
            <input type="text" name="primary_phone" id="primary_phone"
                   class="form-control @error('primary_phone') is-invalid @enderror" value="{{$Client->phone_primary}}"
                   placeholder="Enter Primary Phone">
            @error('primary_phone')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="phone_secondary"><b>Secondary Phone</b></label>
            <input type="text" name="phone_secondary" id="phone_secondary"
                   class="form-control @error('phone_secondary') is-invalid @enderror"
                   value="{{$Client->phone_secondary}}" placeholder="Enter Secondary Phone">
            @error('phone_secondary')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="client_type"><b>Client Type</b><span class="text-danger">*</span></label>
            <select name="client_type" id="client_type" class="form-select @error('client_type') is-invalid @enderror">
                <option selected>--Select Client Type--</option>
                @foreach ($ClientTypes as $clienttype )
                    <option
                        value="{{$clienttype->id}}" {{$Client->client_type == $clienttype->id ? 'selected' : ''}}>{{$clienttype->name}}</option>
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
            <select name="client_type_priority" id="client_type_priority" class="form-select ">
                <option>--Select Priority--</option>
                @foreach ($priorities as $priority)
                    <option value="{{$priority->id}}" {{$Client->client_type_priority == $priority->id ? 'selected' : ''}}>{{$priority->name}}</option>
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
            <select name="contact_through" id="contact_through"
                    class="form-select @error('contact_through') is-invalid @enderror">
                <option selected>--Select Contact Through--</option>
                @foreach ($ContactThrough as $contact_through )
                    <option
                        value="{{$contact_through->id}}" {{$Client->contact_through == $contact_through->id ? 'selected' : ''}}>{{$contact_through->name}}</option>
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
            <select name="interested_on" id="interested_on"
                    class="form-select @error('interested_on') is-invalid @enderror">
                <option selected>--Select Contact Through--</option>
                @foreach ($InterestedsOn as $InterestedOn )
                    <option
                        value="{{$InterestedOn->id}}" {{$Client->interested_on == $InterestedOn->id? 'selected' : ''}}>{{$InterestedOn->name}}</option>
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
                    <option value="{{$businessCategory->id}}" {{$Client->client_business_category_id == $businessCategory->id? 'selected' : ''}}>{{$businessCategory->name}}</option>
                @endforeach
            </select>
            @error('client_business_category_id')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" {{$Client->website ? 'checked' : ''}}>
                <label class="form-check-label" for="flexCheckChecked" >
                  Any Website
                </label>
              </div>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 website"  @if($Client->website) @else style="display: none" @endif>
            <label for="website"><b>Website</b></label>
            <input type="text" name="website" id="website" class="form-control @error('website') is-invalid @enderror" value="{{$Client->website }}" placeholder="Enter Website">
            @error('website')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="email"><b>Email</b></label>
            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{$Client->email }}" placeholder="Enter Email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="present_address"><b>Office Address</b></label>
            <textarea name="present_address" id="present_address" cols="66" rows="4" placeholder="Enter Present Address"
                      class="form-control @error('present_address') is-invalid @enderror">{!! $Client->present_address !!}</textarea>
            @error('present_address')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="country"><b>Country</b></label>
            <select name="country" id="country" class="form-control select2">
                <option value="">--Select Country--</option>
                @if (isset($Client->country_id))
                    <option selected value="{{$Client->country_id}}">
                        {{$Client->country->name}}
                    </option>
                @endif

            </select>
            @error('country')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="states"><b>State</b></label>
            <select name="states" id="states"
                    class="form-control select2">
                <option value="">--Select State--</option>
                @if (isset($Client->state_id))
                    <option selected value="{{$Client->state_id }}">
                        {{$Client->state->name}}
                    </option>
                @endif

            </select>
            @error('states')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="cities"><b>City</b></label>
            <select name="cities" id="cities" class="form-control select2" >
                <option value="">--Select City--</option>
                @if (isset($Client->city_id))
                    <option selected value="{{$Client->city_id }}">
                        {{$Client->city->name}}
                    </option>
                @endif

            </select>
            @error('cities')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="zip"><b>Zip</b></label>
            <input type="text" placeholder="Enter Zip Code" class="form-control" name="zip" id="zip"
                   value="{{$Client->zip}}">
            @error('zip')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <div class="form-check my-3">
                <input class="form-check-input" type="checkbox" value="" id="addressCheckChecked" {{$Client->permanent_address ? 'checked' : ''}}>
                <label class="form-check-label" for="addressCheckChecked" >
                  Other Address
                </label>
              </div>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2 others" @if (!$Client->permanent_address) style="display: none" @endif>
            <label for="permanent_address"><b>Other Address</b></label>
            <textarea name="permanent_address" id="permanent_address_other" cols="66" rows="4"
                      placeholder="Enter Permanent Address"
                      class="form-control @error('permanent_address') is-invalid @enderror">{{$Client->permanent_address}}</textarea>
            @error('permanent_address_other')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2 others" @if (!$Client->country_id_others) style="display: none" @endif  >
            <label for="country_other"><b>Country</b><span class="text-danger">*</span></label>
            <select name="country_other" id="country_other" class="form-control select2" >
                <option value="">--Select Country--</option>
                @if (isset($Client->country_id_others))
                    <option selected value="{{$Client->country_id_others}}">
                        {{$Client->countryOther->name}}
                    </option>
                @endif
            </select>

            @error('country_other')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2 others"  @if (!$Client->country_id_others) style="display: none" @endif>
            <label for="states_other"><b>State</b><span class="text-danger">*</span></label>
            <select name="states_other" id="states_other"
                    class="form-control select2">
                <option value="">--Select State--</option>
                @if (isset($Client->state_id_others))
                    <option selected value="{{$Client->state_id_others }}">
                        {{$Client->stateOther->name}}
                    </option>
                @endif
            </select>
            @error('states_other')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2 others"  @if (!$Client->country_id_others) style="display: none" @endif>
            <label for="cities_other"><b>City</b><span class="text-danger">*</span></label>
            <select name="cities_other" id="cities_other" class="form-control select2">
                <option value="">--Select City--</option>
                @if (isset($Client->city_id_others))
                    <option selected value="{{$Client->city_id_others }}">
                        {{$Client->cityOther->name}}
                    </option>
                @endif
            </select>
            @error('cities_other')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2 others"  @if (!$Client->zip_others) style="display: none" @endif>
            <label for="zip_other"><b>Zip</b><span class="text-danger">*</span></label>
            <input type="text" placeholder="Enter Zip Code" class="form-control" name="zip_other" id="zip_other"
                   value="{{$Client->zip_others}}">
            @error('zip_other')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 mb-2">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="description" rows="10" cols="40"
                      class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                      placeholder="Description...">{{$Client->description}}</textarea>
            @error('description')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary">Update</button>
        </div>
    </div>
</form>
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
            integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function () {
            $('.dropify').dropify();
        });
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
        // client_typeData
        $(document).on('change', '#client_type', function () {
            var id = $("#client_type").val();
            var url = '{{ route('admin.crm.client.type.priority', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function (resp) {
                    $('#client_type_priority').val(resp.priority);
                },
                error: function () {
                    location.reload();
                }
            });
        });
        $(document).on("click", "#flexCheckChecked", function () {
            if ($('#flexCheckChecked').is(":checked"))
                $(".website").show();
            else
                $(".website").hide();
                $("#website").val('');

        });
    </script>
@endpush
