@extends('layouts.dashboard.app')

@section('title', 'Loan Authority')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endpush
@push('script')
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                <span>Authority</span>
            </li>
        </ol>
        <h4 style="color: #0d6efd">{{$authority->name}}</h4>
        <a href="{{ route('admin.loan-authority.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="card mb-4">
           <div class="card-header">
                @include('admin.account.loan.authority.authority-route')
           </div>
           <div class="card-body">
                 <div class="row d-flex align-items-start border-right">
                    <div class="col-md-2 nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link" onclick="setTab('basic')" id="v-pills-basic-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-basic" type="button" role="tab" aria-controls="v-pills-basic" aria-selected="true">Basic</a>
                        <a class="nav-link" onclick="setTab('documents')" id="v-pills-documents-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-documents" type="button" role="tab" aria-controls="v-pills-documents" aria-selected="false">Documents</a>
                        <a class="nav-link" onclick="setTab('identity')" id="v-pills-identity-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-identity" type="button" role="tab" aria-controls="v-pills-identity" aria-selected="false">Identity</a>
                        <a class="nav-link" onclick="setTab('address')" id="v-pills-address-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-address" type="button" role="tab" aria-controls="v-pills-address" aria-selected="false">Address</a>
                    </div>
                    <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-basic" role="tabpanel" aria-labelledby="v-pills-basic-tab" tabindex="0">
                          @include('admin.account.loan.authority.partial.basic')
                        </div>
                        <div class="tab-pane fade " id="v-pills-documents" role="tabpanel" aria-labelledby="v-pills-documents-tab" tabindex="0">
                            @include('admin.account.loan.authority.partial.documents')
                        </div>
                        <div class="tab-pane fade " id="v-pills-identity" role="tabpanel" aria-labelledby="v-pills-identity-tab" tabindex="0">
                            @include('admin.account.loan.authority.partial.identity')
                        </div>
                        <div class="tab-pane fade " id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab" tabindex="0">
                           @include('admin.account.loan.authority.partial.address')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function(){
        var activeTab = localStorage.getItem('activeTabClients');
        if(activeTab){
            $('#v-pills-'+activeTab+'-tab').addClass('active');
            $('#v-pills-'+activeTab).addClass('show active');
        }else{
            $('#v-pills-basic-tab').addClass('active');
            $('#v-pills-basic').addClass('show active');
        }
    })
    function setTab(params){
        localStorage.setItem('activeTabClients', params);
    }
    </script>
@endpush
