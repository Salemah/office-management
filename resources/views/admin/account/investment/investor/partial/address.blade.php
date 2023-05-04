<form action="{{ route('admin.investor.address.update',$investor->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="present_address"><b>Present Address</b><span class="text-danger">*</span></label>
            <textarea name="present_address" id="present_address" cols="66" rows="4" placeholder="Enter Present Address"
                      class="form-control @error('present_address') is-invalid @enderror">{{$investor->present_address}}</textarea>
            @error('present_address')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="permanent_address"><b>Permanent Address</b><span class="text-danger">*</span></label>
            <textarea name="permanent_address" id="permanent_address" cols="66" rows="4"placeholder="Enter Permanent Address"class="form-control @error('permanent_address') is-invalid @enderror">{{$investor->permanent_address}}</textarea>
            @error('permanent_address')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="country"><b>Country</b><span class="text-danger">*</span></label>
            <select name="country" id="country" class="form-control select2">
                <option>--Select Country--</option>
            </select>
            @error('country')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="states"><b>State</b><span class="text-danger">*</span></label>
            <select name="states" id="states"
                    class="form-control select2">
                <option>--Select State--</option>
            </select>
            @error('states')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="cities"><b>City</b><span class="text-danger">*</span></label>
            <select name="cities" id="cities" class="form-control select2">
                <option>--Select City--</option>
            </select>
            @error('cities')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="zip"><b>Zip</b><span class="text-danger">*</span></label>
            <input type="text" placeholder="Enter Zip Code" class="form-control" name="zip" id="zip" value="{{$investor->zip}}">
            @error('zip')
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
    </script>
@endpush
