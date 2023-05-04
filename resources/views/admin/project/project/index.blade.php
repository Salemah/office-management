@extends('layouts.dashboard.app')

@section('title', 'Project')

@push('css')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <style>

    </style>
@endpush

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Project</a>
            </li>
        </ol>
        <div class="">
            <a class="btn btn-sm btn-primary text-white" title="Grid View"
               href="{{ route('admin.project.grid.view') }}">
                <i class='bx bx-grid-alt'></i>
            </a>
            <a class="btn btn-sm btn-warning text-white" title="List View" href="{{ route('admin.projects.index') }}">
                <i class='bx bx-list-ul'></i>
            </a>
            <a class="btn btn-sm btn-success text-white" href="{{ route('admin.projects.create') }}">
                <i class='bx bx-plus'></i> Create
            </a>
        </div>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->
    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <table class="table" id="table">
                    <thead class="table-header fw-semibold dataTableHeader">
                    <tr class="align-middle table">
                        <th class="table-th" width="2%">#SL</th>
                        <th class="table-th" width="11%">Date</th>
                        <th class="table-th" width="12%">Category</th>
                        <th class="table-th" width="25%">Client</th>
                        <th class="table-th" width="10%">Value</th>
                        <th class="table-th" width="10%">Received</th>
                        <th class="table-th" width="10%">Due</th>
                        <th class="table-th" width="10%">Status</th>
                        <th class="table-th" width="10%">Action</th>
                    </tr>
                    </thead>
                    <tfoot class="tfoot active">
                        <th></th>
                        <th>Page Total</th>
                        <!-- <th></th> -->
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>

                    </tfoot>
                </table>
            </div>
        </div>
    </div>
          <!-- Modal -->
 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Project Hold History</h5>
          <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body modal-edit">
        </div>
      </div>
    </div>
</div>


@endsection

@push('script')

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script>
        $(document).ready(function () {
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
                "bFilter": true,
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{route('admin.projects.index')}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'project_date', name: 'project_date', orderable: true, searchable: true},
                    {data: 'project_category', name: 'project_category', orderable: true, searchable: true},
                    {data: 'client', name: 'client', orderable: true, searchable: true},
                    {data: 'value', name: 'value', orderable: true, searchable: true,class:"text-end"},
                    {data: 'receipt', name: 'receipt', orderable: true, searchable: true,class:"text-end"},
                    {data: 'due_amount', name: 'due_amount', orderable: true, searchable: true,class:"text-end"},
                    {data: 'status', name: 'status', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    // Total over all pages
                    total = api
                        .column(6)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Total over this page
                    pageTotal = api
                        .column(6, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                        balance = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'BDT' }).format(pageTotal);

                        pageTotalReceipt = api
                        .column(5, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                        balanceReceipt = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'BDT' }).format(pageTotalReceipt);

                        pageTotalValue = api
                        .column(4, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                        balanceTotal = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'BDT' }).format(pageTotalValue);
                    // Update footer
                    $(api.column(6).footer()).html( balance );
                    $(api.column(5).footer()).html( balanceReceipt );
                    $(api.column(4).footer()).html( balanceTotal );
                },
            });
        });

        // delete Confirm
        function showDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    deleteItem(id);
                }
            });
        };
        // Delete Button
        function deleteItem(id) {
            var url = '{{ route("admin.projects.destroy",":id") }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id ),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#table').DataTable().ajax.reload();
                    if (resp.success === true) {
                        // show toast message
                        toastr.success(resp.message);
                    } else if (resp.errors) {
                        toastr.error(resp.errors[0]);
                    } else {
                        toastr.error(resp.message);
                    }
                }, // success end
                error: function (error) {
                    location.reload();
                } // Error
            })
        }

        // Init board grid so we can drag those columns around.
        boardGrid = new Muuri('.board', {
            dragEnabled: true,
            dragHandle: '.board-column-header'
        });
        function getSelectedLeaveData(id) {
            event.preventDefault();
            var url = '{{ route('admin.project.hold.list',':id') }}';
            $.ajax({
                url: url.replace(':id', id),
                type: "GET",

                success: function (resp) {
                    console.log(resp)
                    // if(resp.type == 1){
                      $('.modal-edit').html(resp.data);

                    // }else{
                    //     $('.modal-show').html(resp.data);
                    // }

                },
                error: function () {
                   // location.reload();
                }
            });
        }
    </script>

@endpush
