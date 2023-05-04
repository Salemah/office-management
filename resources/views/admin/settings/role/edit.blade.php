@extends('layouts.dashboard.app')

@section('title', 'Edit Role')

@push('css')
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{ route('admin.settings.role.index') }}">Edit Role</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-dark" href="{{ route('admin.settings.role.index') }}">
            Back to list
        </a>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <form class="forms-sample" method="POST" action="{{ route('admin.settings.role.update', $role->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group mb-2">
                            <label for="role">Role name</label>
                            <input type="text" class="form-control is-valid" id="role" name="name"
                                   value="{{ $role->name }}" placeholder="Insert Role">
                            <input type="hidden" name="id" value="{{$role->id}}" >
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-12 mb-2">
                            <div class="form-group">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description"  class="form-control " id="description" cols="100" rows="3" placeholder="Enter Role Details">{{$role->description}}</textarea>
                            </div>
                        </div>
                        <div class="col-sm-12 mt-4 text-center"
                        style="border: 1px solid rgba(105, 104, 104, 0.644); padding:10px 5px">
                        <h6 for="exampleInputEmail3"><strong>Assign Permission</strong></h6>
                        {{-- <div class="col-sm-4 text-center"> --}}
                        <div class="form-group">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="all_permission_checkbox"
                                    value="">
                                <span class="custom-control-label">All Permissions</span>
                            </label>
                        </div>
                        {{-- </div> --}}
                    </div>
                        <div class="col-sm-12 mb-4 mt-2">
                            <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th >Name</th>
                                    <th >View</th>
                                    <th >Create</th>
                                    <th >Update</th>
                                    <th >Delete</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td >Employee
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="3" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 3 ? 'checked' : '' }}@endforeach>

                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="12" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 12 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="13" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 13 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="29" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 29 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Documents</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="14" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 14 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="15" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 15 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Identity</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="16" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 16 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="17" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 17 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Address</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="18" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 18 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Qualifications</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="19" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 19 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="20" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 20 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Work Experience</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="21" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 21 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="22" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 22 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Certification</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="23" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 23 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="24" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 24 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Reference</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="25" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 25 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="26" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 26 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Bank Accounts</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="27" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 27 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="28" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 28 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Show</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="30" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 30 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Profile</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="31" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 31 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Import</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="32" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 32 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Employee Export</td>
                                        <td></td>
                                        <td>
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="33" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 33 ? 'checked' : '' }}@endforeach>
                                        </td>

                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                      </tr>

                                      <tr >
                                        <td>Department</td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="34" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 34 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="35" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 35 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td >
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="36" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 36 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="37" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 37 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr >
                                        <td>Designation</td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="38" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 38 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="39" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 39 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td >
                                            <input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="40" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 40 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions-" name="permissions[]"
                                            value="41" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 41 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Marketing Follow Up</td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions-"
                                                name="permissions[]" value="83" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 83 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions-"
                                                name="permissions[]" value="84" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 84 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>
                                            <input type="checkbox" class="form-check-input" id="permissions-"
                                                name="permissions[]" value="85" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 85 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions-"
                                                name="permissions[]" value="86" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 86 ? 'checked' : '' }}@endforeach>
                                        </td>
                                    </tr>
                                      <tr >
                                        <td  >Client</td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions" name="permissions[]"
                                            value="2" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 2 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions" name="permissions[]"
                                            value="64" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 64 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions" name="permissions[]"
                                            value="65" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 65 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input"
                                            id="permissions" name="permissions[]"
                                            value="66" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 66 ? 'checked' : '' }}@endforeach>
                                        </td>
                                      </tr>
                                      <tr>
                                        <td>Client Bank Account</td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions"
                                                name="permissions[]" value="80" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 80 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions"
                                                name="permissions[]" value="81" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 81 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td></td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions"
                                                name="permissions[]" value="82" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 82 ? 'checked' : '' }}@endforeach>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Client Import</td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions"
                                                name="permissions[]" value="59" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 59 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Client Export</td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions"
                                                name="permissions[]" value="60" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 60 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Comments</td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions"
                                                name="permissions[]" value="61" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 61 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Reminder</td>
                                        <td><input type="checkbox" class="form-check-input" id="permissions"
                                                name="permissions[]" value="62" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 62 ? 'checked' : '' }}@endforeach>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                        <tr>
                                            <td>Crm Settings</td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="42" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 42 ? 'checked' : '' }}@endforeach>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Interested On</td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="43" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 43 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="44" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 44 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td ><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="45" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 45 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="46" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 46 ? 'checked' : '' }}@endforeach>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Contact Through</td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="47" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 47 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="48" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 48 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td ><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="49" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 49 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="50" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 50 ? 'checked' : '' }}@endforeach>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Priority</td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="51" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 51 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="52" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 52 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td ><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="53" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 53 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="54" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 54 ? 'checked' : '' }}@endforeach>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Client Type</td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="55" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 55 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="56" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 56 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td ><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="57" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 57 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                id="permissions" name="permissions[]"
                                                value="58" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 58 ? 'checked' : '' }}@endforeach>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Business Category</td>
                                            <td><input type="checkbox" class="form-check-input" id="permissions"
                                                    name="permissions[]" value="87" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 87 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input" id="permissions"
                                                    name="permissions[]" value="88" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 88  ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input" id="permissions"
                                                    name="permissions[]" value="89" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 89 ? 'checked' : '' }}@endforeach>
                                            </td>
                                            <td><input type="checkbox" class="form-check-input" id="permissions"
                                                    name="permissions[]" value="90" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 90 ? 'checked' : '' }}@endforeach>
                                            </td>
                                        </tr>
                                        <tr>
                                    <td>Inventory</td>
                                    <td><input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="4" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 4 ? 'checked' : '' }}@endforeach>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Project</td>
                                    <td><input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="5" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 5 ? 'checked' : '' }}@endforeach>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hrm</td>
                                    <td><input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="6" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 6 ? 'checked' : '' }}@endforeach>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Expense</td>
                                    <td><input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="7" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 7 ? 'checked' : '' }}@endforeach>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Revenue</td>
                                    <td><input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="8" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 8 ? 'checked' : '' }}@endforeach>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Account</td>
                                    <td><input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="9" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 9 ? 'checked' : '' }}@endforeach>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Investment</td>
                                    <td><input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="10" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 10 ? 'checked' : '' }}@endforeach>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Settings</td>
                                    <td><input type="checkbox" class="form-check-input"
                                        id="permissions" name="permissions[]"
                                        value="11" @foreach ($role->permissions as $rPermission){{ $rPermission->id == 11 ? 'checked' : '' }}@endforeach>
                                    </td>
                                </tr>
                                </tbody>
                              </table>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script>
    $('#all_permission_checkbox').on('click', function(){
        if($(this).is(':checked')){
            $('input[type=checkbox]').prop('checked', true);
        }else{
            $('input[type=checkbox]').prop('checked', false);
        }
    });
</script>
@endpush
