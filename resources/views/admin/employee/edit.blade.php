@extends('layouts.dashboard.app')

@section('title', 'Employee Edit ')
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css"
          integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

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
                <a href="{{ route('admin.employee.index') }}">Employee</a>

            </li>
            <li class="breadcrumb-item">
                <span>Update</span>
            </li>
        </ol>
        <a href="{{ route('admin.employee.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.employee.update',$employee->id) }}" enctype="multipart/form-data" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="employee_id"> <b>Employee Id</b> <span class="text-danger">*</span></label>
                            <input type="text" name="employee_id" id="employee_id"
                                   class="form-control "
                                   value="{{$employee->employee_id }}" placeholder="Enter Employee Id">
                            @error('employee_id')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="name"><b>Name</b><span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ $employee->name }}" placeholder="Enter Name">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="designation"><b>Designation</b><span class="text-danger">*</span></label>
                            <select name="designation" id="designation"
                                    class="form-select @error('designation') is-invalid @enderror">
                                <option selected>--Select Designation--</option>
                                @foreach ($designations as $designation )
                                    <option value="{{$designation->id}}" {{ $employee->designation == $designation->id ? 'selected' : '' }}>{{$designation->name}}</option>
                                @endforeach
                            </select>
                            @error('designation')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="department"><b>Department</b><span class="text-danger">*</span></label>
                            <select name="department" id="department"
                                    class="form-select @error('department') is-invalid @enderror">
                                <option>--Select Department--</option>
                                @foreach ($departments as $department )
                                    <option value="{{$department->id}}" {{ $employee->department == $department->id ? 'selected' : '' }}>{{$department->name}}</option>
                                @endforeach
                            </select>
                            @error('departments')
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
                            <input type="text" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{$employee->email}}"
                                   placeholder="Enter Email">
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="phone_primary"><b>Phone Primary</b><span class="text-danger">*</span></label>
                            <input type="text" name="phone_primary" id="phone_primary"
                                   class="form-control @error('phone_primary') is-invalid @enderror"
                                   value="{{$employee->phone_primary}}"
                                   placeholder="Enter Primary Phone">
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
                            <label for="website"><b>Website</b></label>
                            <input type="text" name="website" id="website"
                                   class="form-control @error('website') is-invalid @enderror"
                                   value="{{$employee->website}}"
                                   placeholder="Enter Website">
                            @error('website')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="gender"><b>Gender</b></label>
                            <select name="gender" id="gender"
                                    class="form-select @error('gender') is-invalid @enderror">
                                <option selected>--Select Gender--</option>
                                <option value="1"{{ $employee->gender == 1 ? 'selected' : '' }}>Male</option>
                                <option value="2" {{ $employee->gender == 2 ? 'selected' : '' }}>Female</option>
                                <option value="3" {{ $employee->gender == 3 ? 'selected' : '' }}>Others</option>
                            </select>
                            @error('gender')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>


                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="joining_date"><b>Joining Date</b><span class="text-danger">*</span></label>
                            <input type="date" name="joining_date" id="joining_date"
                                   class="form-control @error('joining_date') is-invalid @enderror"
                                   value="{{$employee->joining_date}}"
                                   placeholder="Enter Joining Date">
                            @error('joining_date')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="joining_salary"><b>Joining Salary</b><span class="text-danger">*</span></label>
                            <input type="text" name="joining_salary" id="joining_salary"
                                   class="form-control @error('joining_salary') is-invalid @enderror"
                                   value="{{$employee->joining_salary}}"
                                   placeholder="Enter Joining Salary">
                            @error('joining_salary')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="current_salary"><b>Current Salary</b><span class="text-danger">*</span></label>
                            <input type="text" name="current_salary" id="current_salary"
                                   class="form-control @error('current_salary') is-invalid @enderror"
                                   value="{{$employee->current_salary}}"
                                   placeholder="Enter Current Salary">
                            @error('current_salary')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="marital_status"><b>Marital Status</b></label>
                            <select name="marital_status" id="marital_status"
                                    class="form-select @error('marital_status') is-invalid @enderror">
                                <option>--Select Marital Status--</option>
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
                            <label for="date_of_birth"><b>Date of Birth</b><span class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" id="date_of_birth"
                                   class="form-control @error('current_salary') is-invalid @enderror"
                                   value="{{$employee->date_of_birth}}"
                                   placeholder="Enter Date of Birth">
                            @error('date_of_birth')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="role"><b>Role</b></label>
                            <select name="role" id="role"
                                    class="form-select @error('role') is-invalid @enderror">
                                <option>--Select Role--</option>
                                <option value="1" selected>1</option>

                            </select>
                            @error('role')
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

                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="present_address"><b>Present Address</b></label>
                            <textarea name="present_address" id="present_address" cols="66" rows="2"
                                      placeholder="Enter Present Address"
                                      class="form-control ">{{ $employee->present_address ? $employee->present_address : '' }}</textarea>
                            @error('present_address')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="permanent_address"><b>Permanent Address</b></label>
                            <textarea name="permanent_address" id="permanent_address" cols="66" rows="2"
                                      placeholder="Enter Permanent Address"
                                      class="form-control ">
                                      {{ $employee->permanent_address ? $employee->permanent_address : '' }}
                                    </textarea>

                            @error('permanent_address')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Identity section --}}
                        <div class="col-12 col-sm-12 col-md-6 mb-2">
                            <div class="form-group">
                                <label for="identity_type"><b>Identity Type</b><span class="text-red">*</span></label>

                                <select name="identity_type[]" id="identity_type"
                                        class="form-control identity_type">
                                    <option value="" selected>--Select Identity</option>
                                    <option value="1">Nid</option>
                                    <option value="2">Birth certificate</option>
                                    <option value="3">Passport</option>
                                    <option value="4">Driving License</option>
                                </select>

                                @error('identity_type')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>

                        <div class="col-11 col-sm-11 col-md-5 mb-2">
                            <div class="form-group">
                                <label for="identity_no"><b>Identity No</b></label>
                                <input type="text" name="identity_no[]" id="identity_no"
                                       value="{{ old('identity_no') }}"
                                       class="form-control @error('identity_no') is-invalid @enderror"
                                       placeholder="Enter Identity No">

                                @error('identity_no')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>

                        <div class="col-sm-1">
                            <div class="form-group">
                                <button style="margin-top:22px" type="button" name="add_identity"
                                        id="add_identity"
                                        class="btn btn-success">+
                                </button>
                            </div>
                        </div>
                        <div id="identity_section" class="mt-3">

                        </div>

                        {{-- Education Section --}}
                        <label> <b>Education</b> </label>

                        <div class=" col-sm-4 ">
                            <div class="form-group">
                                <label for="academic_qualification"><b>Institute Name</b><span
                                        class="text-red">*</span></label>
                                <input type="text" name="academic_qualification[]" id="academic_qualification"
                                       value="{{ old('academic_qualification') }}"
                                       class="form-control @error('academic_qualification') is-invalid @enderror"
                                       placeholder="Enter Institute Name">


                                @error('academic_qualification')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>
                       <div class=" col-sm-4 ">
                            <div class="form-group">
                                <label for="degree"><b>Degree</b></label>
                                <input type="text" name="degree[]" id="degree" value="{{ old('degree') }}"
                                       class="form-control @error('degree') is-invalid @enderror"
                                       placeholder="Enter Degree">

                                @error('degree')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group">
                                <label for="passing_year"><b>Passing Year </b></label>
                                <select name="passing_year[]" id="passing_year" class="form-control @error('passing_year') is-invalid @enderror">
                                    @forelse($years as $year)
                                        <option value="{{$year}}">{{$year}}</option>
                                    @empty
                                        <option value="">--Select--</option>
                                    @endforelse
                                </select>
                                @error('passing_year')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <button style="margin-top:22px" type="button" name="add_education"
                                        id="add_education"
                                        class="btn btn-success">+
                                </button>
                            </div>
                        </div>

                        <div id="education_section" class="mt-3">

                        </div>
                        {{-- EXPERIENCE SECTION --}}
                        <label> <b>Experience </b></label>
                        <div class=" col-sm-4 ">
                            <div class="form-group">
                                <label for="experience"><b>Organization Name</b><span class="text-red">*</span></label>
                                <input type="text" name="experience[]" id="experience"
                                       value="{{ old('experience') }}"
                                       class="form-control @error('experience') is-invalid @enderror"
                                       placeholder="Enter Organization Name">


                                @error('experience')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>

                        <div class=" col-sm-4 ">
                            <div class="form-group">
                                <label for="starting_date"><b>Staring Date</b></label>
                                <input type="date" name="starting_date[]" id="starting_date"
                                       value="{{ old('starting_date') }}"
                                       class="form-control @error('starting_date') is-invalid @enderror"
                                       placeholder="Enter Experience Duration">

                                @error('starting_date')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group">
                                <label for="end_date"><b>End Date </b></label>
                                <input type="date" name="end_date[]" id="end_date" value="{{ old('end_date') }}"
                                       class="form-control @error('end_date') is-invalid @enderror"
                                       placeholder="Enter Identity No">

                                @error('end_date')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <button style="margin-top:22px" type="button" name="add_experience"
                                        id="add_experience"
                                        class="btn btn-success">+
                                </button>
                            </div>
                        </div>
                        <div class=" col-sm-4 ">
                            <div class="form-group">
                                <label for="experience_designation"><b>Designation </b></label>
                                <input type="text" name="experience_designation[]" id="experience_designation" value="{{ old('experience_designation') }}"
                                       class="form-control "
                                       placeholder="Enter Designation">

                                @error('experience_designation')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>
                        <div class=" col-sm-7 ">
                            <div class="form-group">
                                <label for="experience_note"><b>Note</b> </label>
                                <input type="text" name="experience_note[]" id="experience_note" value="{{ old('experience_note') }}"
                                       class="form-control "
                                       placeholder="Enter Note">

                                @error('experience_note')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>


                        <div id="experience_section" class="mt-3">

                        </div>
                        {{-- certification --}}
                        <label><b>Certificate</b></label>

                        <div class=" col-sm-4 ">
                            <div class="form-group">
                                <label for="certification"><b>Organization Name</b><span
                                        class="text-red">*</span></label>
                                <input type="text" name="certification[]" id="certification"
                                       value="{{ old('certification') }}"
                                       class="form-control @error('certification') is-invalid @enderror"
                                       placeholder="Enter Organization Name">


                                @error('certification')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>

                        <div class=" col-sm-4 ">
                            <div class="form-group">
                                <label for="certificate_name"><b>Certification Name</b></label>
                                <input type="text" name="certificate_name[]" id="certificate_name"
                                       value="{{ old('certificate_name') }}"
                                       class="form-control @error('certificate_name') is-invalid @enderror"
                                       placeholder="Enter Certificate Name ">

                                @error('certificate_name')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>
                        <div class=" col-sm-3 ">
                            <div class="form-group">
                                <label for="duration"><b>Duration</b></label>
                                <input type="text" name="duration[]" id="duration" value="{{ old('duration') }}"
                                       class="form-control @error('duration') is-invalid @enderror"
                                       placeholder="Enter Duration">

                                @error('duration')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>

                        <div class="col-sm-1">
                            <div class="form-group">
                                <button style="margin-top:22px" type="button" name="add_certificate"
                                        id="add_certificate"
                                        class="btn btn-success">+
                                </button>
                            </div>
                        </div>
                        <div class=" col-sm-4 ">
                            <div class="form-group">
                                <label for="certificate_year"><b>Year</b></label>
                                <select name="certificate_year[]" id="" class="form-control @error('certificate_year') is-invalid @enderror">
                                    @forelse($years as $year)
                                        <option value="{{$year}}">{{$year}}</option>
                                    @empty
                                        <option value="">--Select--</option>
                                    @endforelse
                                </select>
                                @error('certificate_year')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <div class=" col-sm-7 ">
                            <div class="form-group">
                                <label for="certificate_note"><b>Note</b> </label>
                                <input type="text" name="certificate_note[]" id="certificate_note" value="{{ old('certificate_note') }}"
                                       class="form-control "
                                       placeholder="Enter Note">
                                    @error('certificate_note')
                                <span class="text-danger" role="alert">
                                            <p>{{ $message }}</p>
                                        </span>
                                @enderror

                            </div>
                        </div>

                        <div id="certification_section" class="mt-3">

                        </div>

                        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                            <label for="image"><b>Profile Image</b><span class="text-danger">*</span></label>
                            <input type="file" id="image" data-height="100"
                                   class="dropify form-control @error('image') is-invalid @enderror" name="image">
                            @error('image')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- document --}}

                        <div class="row col-12 col-sm-12 col-md-8 mb-2">
                            <div class="form-group col-md-6">
                                <label for="document_name"><b>Document Name</b></label>
                                <input type="text" id="document_name"
                                       class=" form-control " placeholder="Enter Document Name" name="document_name">
                                @error('document_name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-5 mb-2">
                                <label for="document"><b>File</b></label>
                                <input type="file" id="document"
                                       class=" form-control " name="document">
                                @error('document')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                @enderror
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <button style="margin-top:24px" type="button" name="add_document"
                                            id="add_document"
                                            class="btn btn-success">+
                                    </button>
                                </div>
                            </div>
                            <div id="document_section"></div>
                        </div>

                        {{-- end --}}
                        {{-- Reference --}}
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="reference"><b>Reference</b></label>
                            <select name="reference" id="reference"
                                    class="form-select" >
                                <option>--Select Reference--</option>
                                @foreach ($references as $reference )
                                    <option value="{{$reference->id}}">{{$reference->name}}</option>
                                @endforeach

                            </select>
                            @error('reference')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-5 mb-2">
                            <label for="reference_details"><b>Reference Details</b></label>
                            <input type="text" id="reference_details"
                            class=" form-control " name="reference_details" placeholder="Enter Reference Details">
                            @error('reference_details')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <button style="margin-top:22px" type="button" name="add_reference"
                                        id="add_reference"
                                        class="btn btn-success">+
                                </button>
                            </div>
                        </div>
                        <div id="reference_section" class="mt-3">

                        </div>
                         {{-- Reference  end--}}
                        <div class="form-group col-12 mb-2">
                            <label for="description"><b>Description</b></label>
                            <textarea name="description" id="description" rows="10" cols="40"class="form-control ckeditor"placeholder="Description...">
                                      {{ $employee->description ? $employee->description : '' }}</textarea>
                            @error('description')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-primary">Create</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"
            integrity="sha512-8QFTrG0oeOiyWo/VM9Y8kgxdlCryqhIxVeRpWSezdRRAvarxVtwLnGroJgnVW9/XBRduxO/z1GblzPrMQoeuew=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        CKEDITOR.replace('ckeditor', {
            toolbarGroups: [{
                "name": "styles",
                "groups": ["styles"]
            },
                {
                    "name": "basicstyles",
                    "groups": ["basicstyles"]
                },

                {
                    "name": "paragraph",
                    "groups": ["list", "blocks"]
                },
                {
                    "name": "document",
                    "groups": ["mode"]
                },
                {
                    "name": "links",
                    "groups": ["links"]
                },
                {
                    "name": "insert",
                    "groups": ["insert"]
                },

                {
                    "name": "undo",
                    "groups": ["undo"]
                },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.dropify').dropify();
        });

        var length = $('#card_type_id > option').length;
        var max = 3;
        var i = 0;

        $("#add_identity").click(function () {
            if (i < max) {
                ++i;
                $("#identity_section").append('<div class="row" id="remove_row_identity">' +
                    '<div class="col-12 col-sm-12 col-md-6 mb-2">' +
                         '<div class="form-group">' +
                             '<select name="identity[]" id="identity" class="form-control identity">' +
                                '<option value="" selected>--Select Identity</option>' +
                                '<option value="1">Nid</option>' +
                                 '<option value="2">Birth certificate</option>' +
                                '<option value="3">Passport</option>' +
                                 '<option value="4">Driving License</option>' +
                                 '</select>' +
                             '</div> ' +
                    '</div>' +
                    '<div class="col-11 col-sm-11 col-md-5 mb-2">' +
                         '<div class="form-group">' +
                                 '<input type="text" name="identity_no[]" id="identity_no" value="{{ old('identity_no') }}"class="form-control "placeholder="Enter Identity No">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-sm-1">' +
                        '<div class="form-group">' +
                            '<button id="btn_remove_identity" style="margin-top: 0px" type="button" class="btn btn-danger">-</button> ' +
                        '</div>' +
                    '</div>' +
                '</div>');

            }
            else {
                alert("You've exhausted all of your options");
            }

        });

        $(document).on('click', '#btn_remove_identity', function () {
            $(this).parents('#remove_row_identity').remove();
            i--;
        });

        $("#add_education").click(function () {
            $("#education_section").append(' ' +
                '<div class="row" id="remove_row_education" class="mt-3">' +
                        '<div class=" col-sm-4  mb-2">' +
                            '<div class="form-group">' +
                                '<input type="text" name="academic_qualification[]" id="academic_qualification" value="{{ old('academic_qualification') }}" class="form-control" placeholder="Enter  Institute Name">' +
                           '</div>' +
                        '</div>' +
                        '<div class=" col-sm-4 mb-2 ">' +
                            '<div class="form-group">' +
                                '<input type="text" name="degree[]" id="degree" value="{{ old('degree') }}" class="form-control" placeholder="Enter degree">' +
                            '</div>' +
                        '</div>' +
                         '<div class=" col-sm-3 mb-2 ">' +
                             '<div class="form-group">' +
                                    '<input type="text" name="passing_year[]" id="passing_year" value="{{ old('passing_year') }}"class="form-control placeholder="Enter Passing Year">' +

                                 '</div>' +
                        '</div>' +
                        '<div class="col-sm-1">' +
                             '<div class="form-group">' +
                                     '<button id="btn_remove_education" style="margin-top: 0px" type="button" class="btn btn-danger">-</button>' +
                                '</div>' +
                         '</div>' +
                '</div>');
        });

        $(document).on('click', '#btn_remove_education', function () {
            $(this).parents('#remove_row_education').remove();
        });
        //  add_experience
        $("#add_experience").click(function () {
            $("#experience_section").append('' +
                '<div class="row" id="remove_row_experience" class="mt-3">' +
                     '<div class=" col-sm-4 mb-2 ">' +
                         '<div class="form-group">' +
                                 '<input type="text" name="experience[]" id="experience" value="{{ old('experience') }}"class="form-control" placeholder="Enter Organization Name">' +
                         '</div>' +
                        '</div>' +
                        '<div class=" col-sm-4  mb-2">' +
                            '<div class="form-group">' +
                                    '<input type="date" name="starting_date[]" id="starting_date" value="{{ old('starting_date') }}"class="form-control placeholder="Enter Experience Duration">' +
                            '</div>' +
                        '</div>' +
                        '<div class=" col-sm-3 mb-2 ">' +
                                '<div class="form-group">' +
                                        '<input type="date" name="end_date[]" id="end_date" value="{{ old('end_date') }}"class="form-control placeholder="Enter Identity No">' +
                                '</div>' +
                        '</div>' +
                        '<div class="col-sm-1">' +
                                '<div class="form-group">' +
                                     '<button id="btn_remove_experience" style="margin-top: 0px" type="button" class="btn btn-danger">-</button>' +
                                '</div>' +
                        '</div>' +
                        '<div class=" col-sm-4 mb-2">'+
                            '<div class="form-group">'+
                                '<input type="text" name="experience_designation[]" id="experience_designation" value="{{ old('experience_designation') }}"class="form-control "placeholder="Enter Designation">'+
                                '@error('experience_designation')'+
                                '<span class="text-danger" role="alert">'+
                                            '<p>{{ $message }}</p>'+
                                '</span>'+
                                '@enderror'+
                            '</div>'+
                        '</div>'+
                        '<div class=" col-sm-7 mb-2">'+
                            '<div class="form-group">'+
                                '<input type="text" name="experience_note[]" id="experience_note" value="{{ old('experience_note') }}"class="form-control "placeholder="Enter Note">'+
                                '@error('experience_note')'+
                                '<span class="text-danger" role="alert">'+
                                            '<p>{{ $message }}</p>'+
                                '</span>'+
                                '@enderror'+
                            '</div>'+
                        '</div>'+
                '</div>');
        });

        $(document).on('click', '#btn_remove_experience', function () {
            $(this).parents('#remove_row_experience').remove();
        });

        //  add_certificate
        $("#add_certificate").click(function () {
            $("#certification_section").append('' +
                '<div class="row" id="remove_row_certificate" class="mt-3">' +
                         '<div class=" col-sm-4 mb-2 ">' +
                             '<div class="form-group">' +
                                '<input type="text" name="certification[]" id="certification" value="{{ old('certification') }}"class="form-control" placeholder="Enter Organization Name">' +
                            '</div>' +
                             '</div>' +
                            '<div class=" col-sm-4 mb-2">' +
                                '<div class="form-group">' +
                                        '<input type="text" name="certificate_name[]" id="certificate_name" value="{{ old('certificate_name') }}"class="form-control" placeholder="Enter Certificate Name ">' +
                                '</div>' +
                         '</div>' +
                         '<div class=" col-sm-3 mb-2">' +
                                '<div class="form-group">' +
                                        '<input type="text" name="duration[]" id="duration" value="{{ old('duration') }}"class="form-control" placeholder="Enter Duration">' +
                                '</div>' +
                         '</div>' +
                        '<div class="col-sm-1">' +
                             '<div class="form-group">' +
                                    '<button id="btn_remove_certificate" style="margin-top: 0px" type="button" class="btn btn-danger">-</button>' +
                             '</div>' +
                        '</div>' +
                        '<div class=" col-sm-4 mb-2">'+
                            '<div class="form-group">'+
                              '<select name="year[]" id="" class="form-control">'+
                                    '@forelse($years as $year)'+
                                        '<option value="{{$year}}">{{$year}}</option>'+
                                    '@empty'+
                                        '<option value="">--Select--</option>'+
                                    '@endforelse'+
                                '</select>'+
                                '</div>'+
                        '</div>'+
                        '<div class=" col-sm-7 mb-2">'+
                            '<div class="form-group">'+
                                '<input type="text" name="certificate_note[]" id="certificate_note" value="{{ old('certificate_note') }}"class="form-control "placeholder="Enter Note">'+
                              '</div>'+
                        '</div>'+
                '</div>'
            );
        });

        $(document).on('click', '#btn_remove_certificate', function () {
            $(this).parents('#remove_row_certificate').remove();
        });
        //  add_document
        $("#add_document").click(function () {
            $("#document_section").append('' +
            '<div class="row" id="remove_row_document">' +
                '<div class="form-group col-12 col-sm-12 col-md-6 mb-2">'+
                    '<input type="text" id="document_name"class=" form-control " placeholder="Enter Document Name" name="document_name">'+
                '</div>'+
                '<div class="form-group col-12 col-sm-12 col-md-5 mb-2">'+
                    '<input type="file" id="document"class=" form-control " name="document">'+
                '</div>'+
                '<div class="col-sm-1">' +
                      '<div class="form-group">' +
                             '<button id="btn_remove_document" style="margin-top: 0px" type="button" class="btn btn-danger">-</button>' +
                        '</div>' +
                 '</div>' +
            '</div>'
        );
        });

        $(document).on('click', '#btn_remove_document', function () {
            $(this).parents('#remove_row_document').remove();
        });
        //  add_reference
        $("#add_reference").click(function () {
            $("#reference_section").append('' +
            '<div class="row" id="remove_row_reference" class="mt-3">' +
                '<div class="form-group col-12 col-sm-12 col-md-6 mb-2">'+
                    '<select name="reference"'+ 'id="reference"class="form-select" >'+
                        '<option>--Select Reference--</option>'+
                        '@foreach ($references as $reference )'+
                        '<option value="{{$reference->id}}">{{$reference->name}}</option>'+
                        '@endforeach'+
                    '</select>'+
                '</div>'+
                '<div class="form-group col-12 col-sm-12 col-md-5 mb-2">'+
                    '<input type="text" id="reference_details"class=" form-control " name="reference_details" placeholder="Enter Reference Details">'+
                '</div>'+
            '<div class="col-sm-1">' +
                  '<div class="form-group">' +
                         '<button id="btn_remove_reference" style="margin-top: 0px" type="button" class="btn btn-danger">-</button>' +
                    '</div>' +
                 '</div>' +
            '</div>'
        );
        });

        $(document).on('click', '#btn_remove_reference', function () {
            $(this).parents('#remove_row_reference').remove();
        });
    </script>
@endpush
