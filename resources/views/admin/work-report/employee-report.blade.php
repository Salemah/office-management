@extends('layouts.dashboard.app')

@section('title', 'Employee Report')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.employee.report.crm') }}">Employee Report</a>
            </li>
        </ol>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="form-group col-12 col-sm-12 col-md-2 mb-2">
                        <label for="date_form"><b>Select Date</b></label>
                        <div class="form-group">
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ old('start_date') }}" placeholder="d/m/yy" onchange="search()">

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table " id="table">
                            <thead class="table-light fw-semibold dataTableHeader">
                                <tr class="align-middle table">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Own Client</th>
                                    <th>Reminder</th>
                                    <th>Comment</th>
                                    <th>Assign Client</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <!-- sweetalert -->

    <script>
        $(document).ready(function () {
            search();
        });
        function search(val) {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#table').DataTable({
                order: [],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                processing: true,
                responsive: false,
                serverSide: true,
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                ajax: {
                    url: "{{route('admin.employee.report.crm')}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'name', name: 'name', orderable: true, searchable: true},
                    {data: 'email', name: 'email', orderable: true, searchable: true},
                    {data: 'own', name: 'own', orderable: true, searchable: true},
                    {data: 'reminder', name: 'reminder', orderable: true, searchable: true},
                    {data: 'comment', name: 'comment', orderable: true, searchable: true},
                    {data: 'assign_client', name: 'assign_client', orderable: true, searchable: true},
                    //{data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        };

    </script>
@endpush
