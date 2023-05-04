@extends('layouts.dashboard.app')

@section('title', 'Salary')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                <span>Allowance & Salary </span>
            </li>
        </ol>
        <h4 style="color: #0d6efd">{{$employee->name}}</h4>
        {{--{{dd($employee->id)}}--}}
        <a href="{{ route('admin.employee.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')
<style>
    #allowance_table_filter{
        text-align:right;
    }
    #salary_table_filter{
        float:right;
    }
    #showallck{
        margin-left: 5px;
    vertical-align: middle;
    }
</style>
    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')

    <div class="row">
        <div class="card mb-4">
           <div class="card-header">
                @include('admin.employee.salary.leed-route')
           </div>
            <div class="card-body">
                <div class="row d-flex align-items-start border-right">
                    <div class="col-md-2 nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link" onclick="setTab('salary')" id="v-pills-salary-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-salary" type="button" role="tab" aria-controls="v-pills-salary" aria-selected="true">Salary Increment</a>
                        {{--<a class="nav-link" onclick="setTab('salaryReview')" id="v-pills-salary-review-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-salary-review" type="button" role="tab" aria-controls="v-pills-salary-review" aria-selected="true">Salary Review</a>--}}
                        <a class="nav-link" onclick="setTab('salaryList')" id="v-pills-salaryList-tab" data-coreui-toggle="pill" data-coreui-target="#v-pills-salaryList" type="button" role="tab" aria-controls="v-pills-salaryList" aria-selected="true">Increment List</a>
                    </div>
                    <div class="col-md-9 tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade " id="v-pills-salary" role="tabpanel" aria-labelledby="v-pills-salary-tab" tabindex="0">
                            @include('admin.employee.salary.partial.salary')
                        </div>
                        {{--<div class="tab-pane fade " id="v-pills-salary-review" role="tabpanel" aria-labelledby="v-pills-salary-review-tab" tabindex="0">--}}
                            {{--@include('admin.employee.salary.partial.salary-review')--}}
                        {{--</div>--}}
                        <div class="tab-pane fade " id="v-pills-salaryList" role="tabpanel" aria-labelledby="v-pills-salaryList-tab" tabindex="0">
                            @include('admin.employee.salary.partial.salary-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function(){
        var activeTab = localStorage.getItem('activeTabClient');
        if(activeTab){
            $('#v-pills-'+activeTab+'-tab').addClass('active');
            $('#v-pills-'+activeTab).addClass('show active');
        }else{
            $('#v-pills-allowance-tab').addClass('active');
            $('#v-pills-allowance').addClass('show active');
        }
    })

    function setTab(params){
        localStorage.setItem('activeTabClient', params);
    }
    </script>
@endpush
