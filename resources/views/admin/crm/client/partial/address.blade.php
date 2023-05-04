<form action="{{ route('admin.crm.address.update',$Client->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
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
            <select name="country" id="country_address" class="form-control select2">
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
            <select name="states" id="states_address"
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
            <select name="cities" id="cities_address" class="form-control select2">
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
                <input class="form-check-input" type="checkbox" value="" id="addressChecked" {{$Client->permanent_address ? 'checked' : ''}}>
                <label class="form-check-label" for="addressChecked" >
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
            <label for="country_other"><b>Country</b></label>
            <select name="country_other" id="country_other_address" class="form-control select2">
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
            <label for="states_other"><b>State</b></label>
            <select name="states_other" id="states_other_address"
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
            <label for="cities_other"><b>City</b></label>
            <select name="cities_other" id="cities_other_address" class="form-control select2">
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
            <label for="zip_other"><b>Zip</b></label>
            <input type="text" placeholder="Enter Zip Code" class="form-control" name="zip_other" id="zip_other"
                   value="{{$Client->zip_others}}">
            @error('zip_other')
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
    <script>
        $('#country_address').select2({
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

        $('#states_address').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.state.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        country_id: $('#country_address').val()
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

        $('#cities_address').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.city.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        state_id: $('#states_address').val()
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
        $('#country_other_address').select2({
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

        $('#states_other_address').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.state.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        country_id: $('#country_other_address').val()
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

        $('#cities_other_address').select2({
            ajax: {
                url: '{{route('admin.employee.details.address.city.search')}}',
                dataType: 'json',
                type: "POST",
                data: function (params) {
                    var query = {
                        search: params.term,
                        state_id: $('#states_other_address').val()
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

        $(document).on("click", "#addressChecked", function () {
            if ($('#addressChecked').is(":checked"))
                $(".others").show();
            else
                $(".others").hide();
                $("#permanent_address_other").val('');
                $("#country_other").val(null).trigger('change');
                $("#states_other").val(null).trigger('change');
                $("#cities_other").val(null).trigger('change');
                $("#zip_other").val('');


        });
    </script>
@endpush
