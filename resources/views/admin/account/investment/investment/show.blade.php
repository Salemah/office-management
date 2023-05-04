@extends('layouts.dashboard.app')

@section('title', 'Investment')
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
                <span>Investment</span>
            </li>
        </ol>
        <h4 style="color: #0d6efd">{{$investor->name}}</h4>
        <a href="{{ route('admin.investment.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="card mb-4">
           <div class="card-header">
                @include('admin.account.investment.investment.investment-route')
           </div>
            <div class="card-body">
                 <div class="row d-flex align-items-start border-right">
                    <div class="col-md-2 nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link" onclick="setTab('basic')" id="v-pills-basic-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-basic" type="button" role="tab" aria-controls="v-pills-basic" aria-selected="true">Investment </a>
                        <a class="nav-link" onclick="setTab('investmentlist')" id="v-pills-investmentlist-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-investmentlist" type="button" role="tab" aria-controls="v-pills-investmentlist" aria-selected="false">Investment List</a>
                    </div>
                     <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-basic" role="tabpanel" aria-labelledby="v-pills-basic-tab" tabindex="0">
                          @include('admin.account.investment.investment.partial.basic')
                        </div>
                        <div class="tab-pane fade " id="v-pills-investmentlist" role="tabpanel" aria-labelledby="v-pills-investmentlist-tab" tabindex="0">
                            @include('admin.account.investment.investment.partial.investment-list')
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
