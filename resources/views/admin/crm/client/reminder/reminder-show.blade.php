@extends('layouts.dashboard.app')

@section('title', 'Client ')

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
        <h4 style="color: #0d6efd">{{$Client->name}}</h4>
        <a href="{{ route('admin.crm.client.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="card mb-4">
           <div class="card-header">
                @include('admin.crm.client.client-route')
           </div>
            <div class="card-body">
                <div class="row d-flex align-items-start border-right">
                    <div class="col-md-2 nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link" onclick="setTab('reminder')" id="v-pills-reminder-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-reminder" type="button" role="tab" aria-controls="v-pills-reminder" aria-selected="true">Reminder</a>
                        <a class="nav-link" onclick="setTab('addreminder')" id="v-pills-addreminder-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-addreminder" type="button" role="tab" aria-controls="v-pills-addreminder" aria-selected="true">Add Reminder</a>
                    </div>

                    <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-reminder" role="tabpanel" aria-labelledby="v-pills-reminder-tab" tabindex="0">
                            @include('admin.crm.client.reminder.partial.reminder')
                        </div>
                        <div class="tab-pane fade " id="v-pills-addreminder" role="tabpanel" aria-labelledby="v-pills-addreminder-tab" tabindex="0">
                            @include('admin.crm.client.reminder.partial.add-reminder')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function(){
        var activeTab = localStorage.getItem('activeTabClientreminder');
        if(activeTab){
            $('#v-pills-'+activeTab+'-tab').addClass('active');
            $('#v-pills-'+activeTab).addClass('show active');
        }else{
            $('#v-pills-reminder-tab').addClass('active');
            $('#v-pills-reminder').addClass('show active');
        }
    })

    function setTab(params){
        localStorage.setItem('activeTabClientreminder', params);
    }



    </script>
@endpush
