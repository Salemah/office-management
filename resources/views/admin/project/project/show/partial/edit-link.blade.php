@extends('layouts.dashboard.app')

@section('title', 'Project')

@push('css')
<style>
    .card{
        box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px;
   }
</style>

@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                <a href="{{ route('admin.projects.index') }}">Link</a>
            </li>
            <li class="breadcrumb-item">
                Edit
            </li>
        </ol>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.link.update',$project_link->id) }}"
      method="POST">
    @csrf
    @method('PUT')
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="cpanel_link"> <b>Cpanel Link </b></label>
                            <input type="text"  id="cpanel_link" class="form-control " name="cpanel_link"placeholder="Enter Cpanel Link" value="{{$project_link->cpanel_link}}">
                            @error('cpanel_link')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="cpanel_password"><b> Cpanel Password</b></label>
                            <input type="text"  id="cpanel_password" class="form-control " name="cpanel_password"   placeholder="Enter Cpanel Password" value="{{$project_link->cpanel_password}}">
                            @error('cpanel_password')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="website"><b> Web Link</b></label>
                            <input type="text"  type="text" name="website" id="website" class="form-control @error('website') is-invalid @enderror"  placeholder="Enter Web Link" value="{{$project_link->web_link}}">
                            @error('website')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="git_link"><b> Git Link</b></label>
                            <input type="text"  id="git_link" class="form-control " name="git_link"   placeholder="Enter Git Link" value="{{$project_link->git_link}}">
                            @error('git_link')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        @if ($user_emails)
                            @foreach ($user_emails as $key=>$email)
                                <div class="row mt-2" id="remove_row_user">
                                    <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                        @if ($key==0)
                                        <label for="user_role"><b>User Role</b></label>
                                        @endif
                                        <input type="text"  id="user_role" class="form-control " name="user_role[]"   placeholder="Enter User Role" value="{{$user_roles[$key]}}">
                                        @error('user_role')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
                                        @if ($key==0)<label for="email"><b>Email</b></label> @endif
                                        <input type="email"  id="email" class="form-control " name="email[]"   placeholder="Enter User Email" value="{{$email}}">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group col-12 col-sm-12 col-md-3 mb-2">
                                        @if ($key==0)<label for="password"><b>Password</b></label> @endif
                                        <input type="text"  id="password" class="form-control " name="password[]"   placeholder="Enter User Password" value="{{$user_passwords[$key]}}">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                        <div class="form-group col-sm-1 ">
                                            <button  @if($key==0)style="margin-top:22px" @endif class="jDeleteRow btn btn-danger btn-icon text-white"type="button" id="btn_remove_user" >&times;
                                            </button>
                                        </div>


                                </div>
                            @endforeach
                        @endif
                        <div class="expense-body">

                        </div>
                        {{-- append data --}}

                        <div class="col-sm-4 my-3 ">
                            <button type="button" id="addRow" class="btn btn-sm btn-success text-white">
                                + Add User
                            </button>
                        </div>
                        {{--  --}}
                        <input type="hidden" name="project_id" id="project_id" value="{{ $project_link->project_id }}">
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-primary mb-3">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</form>
    <div class="mb-5"></div>


@endsection
@push('script')

<script>
    $(document).ready(function () {
            var max_field = 5;
            var wrapper = $(".expense-body");
            var x = 0;
            $("#addRow").click(function () {
                    x++;
                    $(wrapper).append('<div class="row mt-2" id="link-row-' + x + '">'+
                                        '<div class="form-group col-12 col-sm-12 col-md-4 mb-2">'+
                                            '<input type="text"  id="user_role" class="form-control " name="user_role[]"   placeholder="Enter User Role">'+
                                        '</div>'+
                                        '<div class="form-group col-12 col-sm-12 col-md-4 mb-2">'+
                                            '<input type="email"  id="email" class="form-control " name="email[]"   placeholder="Enter User Email">'+
                                        '</div>'+
                                        '<div class="form-group col-12 col-sm-12 col-md-3 mb-2">'+
                                            '<input type="text"  id="password" class="form-control " name="password[]"   placeholder="Enter User Password">'+
                                        '</div>'+
                                        '<div class="form-group col-sm-1 ">'+
                                            '<button type="button"  class=" btn btn-danger btn-icon waves-effect waves-light text-white" onclick="linkRemove(' + x + ')">' +
                                                        '&times;' +
                                            '</button>' +
                                       ' </div>'+
                                     '</div>');
            });
    });

    function linkRemove(id) {
        $('#link-row-' + id).remove();
    }
    $(document).on('click', '#btn_remove_user', function () {
            $(this).parents('#remove_row_user').remove();
        });
</script>

@endpush
