@extends('layouts.dashboard.app')

@section('title', 'Sale')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        #table_filter, #table_paginate {
            float: right;
        }
        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        .table-responsive {
            overflow-x: hidden !important;
        }
        .select2-container--default .select2-selection--single{
            padding:6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }
        .select2-container--open {
            z-index: 9999999
        }
    </style>
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
                <a href="#">Sale</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.inventory.sale.create') }}">
            <i class='bx bx-plus'></i> Create
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
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table border mb-0" id="table">
                            <thead class="table-light fw-semibold">
                            <tr class="align-middle table">
                                <th>#</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Grand Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                {{-- <th>Status</th> --}}
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" >
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Received Payment</h5>
              <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-edit">
                <form action="{{ route('admin.inventory.received.payment') }}" enctype="multipart/form-data" method="POST" >
                    @csrf
                    <div class="row">
                        <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="" >
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="date"><b>Date</b><span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('datee') is-invalid @enderror"value="{{ old('date') }}" name="date" id="datepicker">
                            @error('date')
                                <span class="alert text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="received_amount"><b>Receivable Amount</b><span class="text-danger">*</span></label>
                            <input type="number" step="any" id="received_amount" class="form-control @error('received_amount') is-invalid @enderror"value="{{ old('received_amount') }}" name="received_amount" readonly>
                            <input type="hidden"  id="sale_id" class="form-control @error('sale_id') is-invalid @enderror"value="{{ old('sale_id') }}" name="sale_id">
                            @error('received_amount')
                                <span class="alert text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="paying_amount"><b>Receiving Amount</b><span class="text-danger">*</span></label>
                            <input type="number" step="any" oninput="payingAmountCheck()" id="paying_amount" class="form-control @error('paying_amount') is-invalid @enderror"value="{{ old('paying_amount') }}" name="paying_amount">
                            @error('paying_amount')
                                <span class="alert text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                            <label for="paying_amount"><b>Due :</b></label>
                           <p id="change"></p>
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 transaction" >
                            <label for="transaction_way"><b>Transaction Type</b><span
                                    class="text-danger">*</span></label>
                            <select name="transaction_way" id="transaction_way" class="form-control"
                                    onchange="expenseTransactionWay()">
                                <option value="" selected>-- Select --</option>
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                            </select>
                            @if ($errors->has('transaction_way'))
                                <span class="alert text-danger">
                                    {{ $errors->first('transaction_way') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 cash" style="display: none">
                            <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span><span class="text-info " id="cash-balance"
                                style="display: none"></span></label>
                            <select name="cash_account_id" id="cash_account_id"class="form-select " onchange="getBalance()">
                                <option  value=""  selected>--Select Cash Account--</option>
                                @foreach ($cash_account as $account)
                                     <option value="{{$account->id}}" >{{$account->name}}</option>
                                @endforeach
                            </select>
                                @error('cash_account_id')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way  "
                             style="display: none">
                            <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span><span class="text-info " id="balance"
                                style="display: none"></span></label>
                            <select name="account_id" id="account_id" class="form-control" onchange="getBalance()">
                                <option value="" selected>-- Select Bank Account --</option>
                                @foreach ($bankAccounts as $bankAccount)
                                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                        | {{ $bankAccount->account_number }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('account_id'))
                                <span class="alert text-danger">
                                    {{ $errors->first('account_id') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 bank-way "
                             style="display: none">
                            <label for="cheque_number"><b>Cheque Number / Transaction</b><span class="text-danger">*</span></label>
                            <input type="hidden" value="" id="amount_balance">
                            <input type="text" name="cheque_number" id="cheque_number"
                                   class="form-control" value="{{ old('cheque_number') }}" placeholder=" ...">
                            @if ($errors->has('cheque_number'))
                                <span class="alert text-danger">
                                    {{ $errors->first('cheque_number') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-12 mb-2"
                             >
                            <label for="note"><b>Payment Note</b></label>
                            <textarea rows="3" class="form-control" name="note"></textarea>
                            @if ($errors->has('note'))
                                <span class="alert text-danger">
                                    {{ $errors->first('note') }}
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
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
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{route('admin.inventory.sale.index')}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex",      name: "DT_RowIndex",       orderable: false,  searchable: false},
                    {data: 'date',            name: 'date',             orderable: true,   searchable: true},
                    {data: 'reference_no',             name: 'reference_no',              orderable: true,   searchable: true},
                    {data: 'customer',             name: 'customer',     orderable: true,   searchable: true},
                    {data: 'total_qty',             name: 'total_qty',     orderable: true,   searchable: true},
                    {data: 'grand_total',          name: 'grand_total',   orderable: true,   searchable: true},
                    {data: 'paid',          name: 'paid',   orderable: true,   searchable: true},
                    {data: 'due',          name: 'due',   orderable: true,   searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
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
            var url = '{{ route("admin.inventory.sale.destroy",":id") }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
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
        function getSelectedUserData(id) {
            var url = '{{ route('admin.inventory.sale.sale.search', ':id') }}';
            $.ajax({
                type: "GET",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                  $('#received_amount').val(resp.grand_total-resp.paid_amount );
                  $('#warehouse_id').val(resp.warehouse_id );
                  $('#paying_amount').val(resp.grand_total -resp.paid_amount);
                  $('#sale_id').val(resp.id);
                },
                error: function () {
                   // location.reload();
                }
            });
        }
        $('#account_id').select2({
            ajax: {
                url: '{{route('admin.account.deposit.bank.search')}}',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                        transaction_way : $("#transaction_way").val()
                    }
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });
        $('#cash_account_id').select2({
            ajax: {
                url: '{{route('admin.account.deposit.bank.search')}}',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                       transaction_way : $("#transaction_way").val()
                    }
                    return query;
                },
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                value: item.id,
                                id: item.id,
                            }
                        })
                    };
                }
            }
        });
        function expenseTransactionWay() {
            var transaction_way = $("#transaction_way").val();
            if (transaction_way == 2) {
                $('.bank-way').show();
                $('#cash_account_id').val('');
                $('.cash').hide();
            } else if(transaction_way == 1) {
                 $('.bank-way').hide();
                 $('#account_id').val('');
                 $('.cash').show();
            }
             else{
                $('.bank-way').hide();
                $('.cash').hide();
                $('#account_id').val('');
             }
        };
        function getBalance() {
                var totalBalance = $('#total_balance').val();
                var transactionWay = $('#transaction_way').val();
              if (transactionWay == 1) {
                var accountId = $('#cash_account_id').val();
              }
              else{
                var accountId = $('#account_id').val();
              }
                    var url = '{{ route('admin.account.bank.account.balance', ':id') }}';
                    $.ajax({
                        type: "GET",
                        url: url.replace(':id', accountId),
                        success: function (resp) {
                            console.log(resp);
                            if (transactionWay == 1){
                                $('#cash-balance').show();
                                document.getElementById('cash-balance').innerHTML = '( ' + resp + ' )';
                            }else{
                                $('#balance').show();
                                document.getElementById('balance').innerHTML = '( ' + resp + ' )';
                            }
                                $('#amount_balance').val(resp);
                                document.getElementById('amount').max = resp;
                        }, // success end
                        error: function (error) {
                            location.reload();
                        } // Error
                    })
        }

        function checkAmount(amount) {
            var amountBalance = $('#amount_balance').val();
            var totalBalance = $('#total_balance').val();
            console.log(totalBalance);
            var amountBalance = $('#amount_balance').val();
            if (parseFloat(amount) < parseFloat(totalBalance)) {
                swal({
                    title: `Alert?`,
                    text: "You don't have enough balance.",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('.amount').val(0);
                        $('#total_balance').val(0);
                        $('#adjustment_balance').val(0);
                        $('#total').val(0);
                    }

                });
            }
        }

        function payingAmountCheck(){
                  var receive = $('#received_amount').val();
                  var pay  = $('#paying_amount').val();

                  var due =  parseFloat(receive)-parseFloat(pay);
                  document.getElementById("change").innerHTML =due;
                  if (parseFloat(receive) < parseFloat(pay)) {
                swal({
                    title: `Alert?`,
                    text: "Paying Amount Cant Greater Than Receive Amount",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#paying_amount').val(0);
                        document.getElementById("change").innerHTML = '';
                    }
                });
            }
        }
    </script>
@endpush
