@extends('layouts.dashboard.app')

@section('title', 'Employee ')


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
                <span>Create</span>
            </li>
        </ol>
        <p class="text-warning">Employee default password is "employee". </p>
        <a href="{{ route('admin.employee.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <form action="{{ route('admin.employee.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="employee_id"> <b>Employee Id</b> <span class="text-danger">*</span></label>
                            <input type="text" name="employee_id" id="employee_id"
                                   class="form-control @error('employee_id') is-invalid @enderror"
                                   value="{{ old('employee_id') }}" placeholder="Enter Employee Id">
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
                                   value="{{ old('name') }}" placeholder="Enter Name">
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
                                <option value="" selected>--Select Designation--</option>
                                @foreach ($designations as $designation )
                                    <option value="{{$designation->id}}">{{$designation->name}}</option>
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
                                <option value="" selected>--Select Department--</option>
                                @foreach ($departments as $department )
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                @endforeach
                            </select>
                            @error('department')
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="email"><b>Email</b><span class="text-danger">*</span></label>
                            <input type="text" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
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
                                   value="{{ old('phone_primary') }}"
                                   placeholder="Enter Primary Phone">
                            @error('phone_primary')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="joining_date"><b>Joining Date</b><span class="text-danger">*</span></label>
                            <input type="date" name="joining_date" id="joining_date"
                                   class="form-control @error('joining_date') is-invalid @enderror"
                                   value="{{ old('joining_date') }}"
                                   placeholder="Enter Joining Date">
                            @error('joining_date')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="joining_salary"><b>Joining Salary</b><span class="text-danger">*</span></label>
                            <input type="number" name="joining_salary" id="joining_salary"
                                   class="form-control @error('joining_salary') is-invalid @enderror"
                                   value="{{ old('joining_salary') }}"
                                   placeholder="Enter Joining Salary">
                            @error('joining_salary')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
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
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="role"><b>Role</b></label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                                <option value="" selected>--Select role--</option>
                                    @foreach ($roles as $key => $role )
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                {{-- <option value="3" selected>Admin</option>
                                <option value="2">Admin</option>
                                <option value="3">Admin</option> --}}
                            </select>
                            @error('role')
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

