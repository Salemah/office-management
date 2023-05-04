@extends('layouts.dashboard.app')

@section('title', 'Create Role')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{ route('admin.settings.role.create') }}">Create Role</a>
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
                <form class="forms-sample" method="POST" action="{{ route('admin.settings.role.store') }}">
                    @csrf
                    <div class="col-sm-12 mb-2">
                        <div class="form-group">
                            <label for="role"><b>Role</b><span class="text-danger">*</span></label>
                            <input type="text" class="form-control is-valid" id="role" name="name"
                                placeholder="Role Name" required>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-2">
                        <div class="form-group">
                            <label for="description"><b>Description</b></label>
                            <textarea name="description" class="form-control " id="description" cols="100"
                                rows="3"placeholder="Enter Role Details"></textarea>
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

                    <div class="col-sm-12 mb-4 mt-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>View</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Employee
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="3">

                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="12">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="13">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="29">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Documents</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="14">
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="15">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Identity</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="16">
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="17">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Address</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="18">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Qualifications</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="19">
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="20">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Work Experience</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="21">
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="22">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Certification</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="23">
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="24">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Reference</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="25">
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="26">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Bank Accounts</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="27">
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="28">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Show</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="30">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Profile</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="31">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Import</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="32">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Employee Export</td>
                                    <td></td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="33">
                                    </td>

                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                </tr>

                                <tr>
                                    <td>Department</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="34">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="35">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="36">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="37">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Designation</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="38">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="39">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="40">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="41">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Marketing Follow Up</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="83">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="84">
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="85">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions-"
                                            name="permissions[]" value="86">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Client</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="2">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="64">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="65">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="66">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Client Bank Account</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="80">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="81">
                                    </td>
                                    <td></td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="82">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Client Import</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="59">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Client Export</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="60">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Comments</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="61">
                                    </td>
                                    <td>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Reminder</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="62">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Crm Settings</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="42">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Interested On</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="43">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="44">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="45">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="46">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact Through</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="47">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="48">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="49">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="50">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Priority</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="51">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="52">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="53">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="54">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Client Type</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="55">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="56">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="57">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="58">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Business Category</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="87">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="88">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="89">
                                    </td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="90">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Inventory</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="4">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Project</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="5">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hrm</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Expense</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="7">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Revenue</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="8">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Account</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="9">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Investment</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="10">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Settings</td>
                                    <td><input type="checkbox" class="form-check-input" id="permissions"
                                            name="permissions[]" value="11">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group mb-4 text-center">
                        <button title="Submit Button" type="submit" class="btn btn-primary btn-rounded">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $('#all_permission_checkbox').on('click', function() {
            if ($(this).is(':checked')) {
                $('input[type=checkbox]').prop('checked', true);
            } else {
                $('input[type=checkbox]').prop('checked', false);
            }
        });
    </script>
@endpush
