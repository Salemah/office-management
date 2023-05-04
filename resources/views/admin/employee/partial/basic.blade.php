@push('css')
    {{--select 2 css cdn end  --}}
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
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

<form action="{{ route('admin.employee.update',$employee->id) }}" enctype="multipart/form-data" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <input type="file" id="image" class="dropify form-control @error('image') is-invalid @enderror"
                   @if ($employee->image) data-default-file="{{ asset('img/employee/' . $employee->image) }}"
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
            <label for="employee_id"> <b>Employee Id</b> <span class="text-danger">*</span></label>
            <input type="text" name="employee_id" id="employee_id"
                   class="form-control @error('employee_id') is-invalid @enderror"
                   value="{{$employee->employee_id}}" placeholder="Enter Employee Id">
            @error('employee_id')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="name"><b>Name</b><span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                   class="form-control @error('name') is-invalid @enderror" value="{{$employee->name}}"
                   placeholder="Enter Name">
            @error('name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="designation"><b>Designation</b><span class="text-danger">*</span></label>
            <select name="designation" id="designation"
                    class="form-control select2">
                <option selected value="{{$employee->designation}}">
                    {{$employee->designations->name}}
                </option>
            </select>
            @error('designation')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="department"><b>Department</b></label>
            <select name="department" id="department" class="form-control">
                <option value="{{$employee->department}}" selected>
                    {{$employee->departments->name}}
                </option>
            </select>
            @error('department')
            <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
             </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="father_name"><b>Father Name</b></label>
            <input type="text" name="father_name" id="father_name"
                   class="form-control @error('father_name') is-invalid @enderror"
                   value="{{$employee->father_name}}" placeholder="Enter Father Name">
            @error('father_name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="mother_name"><b>Mother Name</b></label>
            <input type="text" name="mother_name" id="mother_name"
                   class="form-control @error('mother_name') is-invalid @enderror"
                   value="{{$employee->mother_name}}" placeholder="Enter Mother Name">
            @error('mother_name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="email"><b>Email</b><span class="text-danger">*</span></label>
            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{$employee->email}}"
                   placeholder="Enter Email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="blood_group"><b>Blood Group</b><span class="text-danger">*</span></label>
            <select name="blood_group" id="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                <option value="" selected>--Select Department--</option>
                @foreach ($bloodgroups as $bloodgroup )
                    <option
                        value="{{$bloodgroup->id}}" {{$employee->blood_group == $bloodgroup->id ? 'selected' : ' ' }}>{{$bloodgroup->name}}</option>
                @endforeach
            </select>
            @error('blood_group')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="gender"><b>Gender</b></label>
            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                <option value="" selected>--Select Gender--</option>
                <option value="1" {{$employee->gender == 1? 'selected' : ''}}>Male</option>
                <option value="2" {{$employee->gender == 2 ? 'selected' : ''}}>Female</option>
                <option value="3" {{$employee->gender == 3 ? 'selected' : ''}}>Others</option>
            </select>
            @error('gender')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="phone_primary"><b>Phone Primary</b><span class="text-danger">*</span></label>
            <input type="text" name="phone_primary" id="phone_primary"
                   class="form-control @error('phone_primary') is-invalid @enderror"
                   value="{{$employee->phone_primary}}" placeholder="Enter Primary Phone">
            @error('phone_primary')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="phone_secondary"><b>Phone Secondary</b></label>
            <input type="text" name="phone_secondary" id="phone_secondary"
                   class="form-control @error('phone_secondary') is-invalid @enderror"
                   value="{{$employee->phone_secondary}}"
                   placeholder="Enter Phone Secondary">
            @error('phone_secondary')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="joining_date"><b>Joining Date</b><span class="text-danger">*</span></label>
            <input type="date" name="joining_date" id="joining_date"
                   class="form-control @error('joining_date') is-invalid @enderror"
                   value="{{$employee->joining_date}}" placeholder="Enter Joining Date">
            @error('joining_date')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="marital_status"><b>Marital Status</b></label>
            <select name="marital_status" id="marital_status"
                    class="form-select @error('marital_status') is-invalid @enderror">
                <option value="" selected>--Select Marital Status--</option>
                <option value="1"{{ $employee->marital_status == 1 ? 'selected' : '' }}>Married</option>
                <option value="2" {{ $employee->marital_status == 2 ? 'selected' : '' }}>Unmarried</option>
                <option value="3" {{ $employee->marital_status == 3 ? 'selected' : '' }}>Separated</option>
            </select>
            @error('dmarital_status')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="website"><b>Website</b></label>
            <input type="text" name="website" id="website" class="form-control @error('website') is-invalid @enderror"
                   value="{{$employee->website}}" placeholder="Enter Website">
            @error('website')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="warehouse"><b>Warehouse</b><span class="text-danger">*</span></label>
            <select name="warehouse" id="warehouse"
                    class="form-select @error('warehouse') is-invalid @enderror">
                <option value="" selected>--Select Warehouse--</option>
                @foreach ($warehouses as $warehouse )
                    <option value="{{$warehouse->id}}" {{$employee->warehouse == $warehouse->id ?  'selected' : '' }}>{{$warehouse->name}}</option>
                @endforeach
            </select>
            @error('warehouse')
            <span class="invalid-feedback" role="alert">
                 <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="date_of_birth"><b>Date of Birth</b><span class="text-danger">*</span></label>
            <input type="date" name="date_of_birth" id="date_of_birth"
                   class="form-control @error('date_of_birth') is-invalid @enderror"
                   value="{{$employee->date_of_birth}}" placeholder="Enter Date of Birth">
            @error('date_of_birth')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="role"><b>Status</b></label>
            <select name="status" id="status" class="form-select @error('role') is-invalid @enderror">
                <option value="1" selected {{ $employee->status == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ $employee->status == 0 ? 'selected' : '' }} >Inactive</option>
            </select>
            @error('status')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 mb-2">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="description" rows="10" cols="40"
                      class="form-control description"
                      value="{{ old('description') }}"
                      placeholder="Description...">{{$employee->description}}</textarea>
            @error('description')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        @can('employee_update')
            <div class="form-group">
                <button type="submit" class="btn btn-sm btn-primary">Update</button>
            </div>
        @endcan

    </div>
</form>

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
            integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>

        ckEditor('description');

        $(document).ready(function () {
            $('.dropify').dropify();
        });

        $('#department').select2({
            ajax: {
                url: '{{route('admin.employee.details.department.search')}}',
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

        $('#designation').select2({
            ajax: {
                url: '{{route('admin.employee.details.designation.search')}}',
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
