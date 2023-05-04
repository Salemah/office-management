@extends('layouts.dashboard.app')

@section('title', 'employee salary edit')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .text-danger strong {
            font-size: 11px;
        }
        .responsive-table tr td .responsive-table-title {
            width: 50%;
            font-weight: 600;
            display: none;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--single{
            padding:6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }


        @media (min-width: 200px ) and (max-width: 1130px ) {
            .responsive-table {
                width: 100%;
            }
            .responsive-table th {
                display: none;
            }
            .responsive-table .responsive-table-tr {
                display: grid;
                padding: 3%;
                border: 1px solid #d5d5d5;
                border-radius: 5px;
                margin-bottom: 10px;
            }
            .responsive-table tr td {
                display: flex;
                align-items: center;
            }

            .responsive-table tr td .responsive-table-title {
                display: block;
            }
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
                <a href="{{route('admin.salaryReport.index')}}">Expense </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.salaryReport.create')}}">Create</a>
            </li>
        </ol>
        <a href="{{route('admin.salaryReport.index')}}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <!-- End:Alert -->

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{route('admin.salary-report.update',$data->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-2">
                            <div class="col-sm-6 mb-2">
                                <div class="form-group">
                                    <label for="employee_id"><b>Employee</b><span class="text-danger">*</span></label>
                                    <select class="form-control employee_id " id="employee_id"name="employee_id" >
                                        <option value="" selected>--Select Employee--</option>
                                        @foreach($employees as $employee)
                                            <option value="{{$employee->id}}" @if($employee->id == $data->employee_id) {{'selected'}} @endif>{{$employee->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                    <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-3 ">
                                <div class="form-group">
                                    <label for="amount"><b>Amount</b><span class="text-danger">*</span></label>
                                    <input class="form-control amount" value="{{$data->amount}}" id="amount" type="number" placeholder="0.00" name="amount" >
                                    @error('amount')
                                    <span class="text-danger" role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="month"><b>Select Month</b> <span class="text-danger">*</span> </label>
                                <input type="month" id="month" value="{{$data->month}}" name="month" class="form-control"  onkeyup="check_data()">
                                @error('month')
                                <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="form-group col-12 col-sm-12 col-md-4 mb-2 transaction" >
                                <label for="transaction_way"><b>Transaction Type</b><span
                                            class="text-danger">*</span></label>
                                <select name="transaction_way" id="transaction_way" class="form-control"
                                        onchange="expenseTransactionWay()">
                                    <option value="" selected>-- Select --</option>
                                    <option value="1" @if($data->transaction_way == 1) selected @endif>Cash</option>
                                    <option value="2" @if($data->transaction_way == 2) selected @endif>Bank</option>
                                </select>
                                @if ($errors->has('transaction_way'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('transaction_way') }}
                                    </span>
                                @endif
                            </div>
                            <div style="display:none;" class="form-group col-12 col-sm-12 col-md-6 mb-2 cash">
                                <label for="account_id"> <b>Cash Account</b><span class="text-danger">*</span><span class="text-info " id="cash-balance"
                                                                                                                    ></span></label>
                                <select  style="display:none;" name="cash_account_id" id="cash_account_id"class="form-select " onchange="getBalance()">
                                    <option  value=""  selected>--Select Cash Account--</option>
                                    @foreach ($cash_account as $account)
                                        <option value="{{$account->id}}" @if($account){{$account->id == $data->account_id ? 'selected' : ''}} @endif >{{$account->name}}</option>
                                    @endforeach
                                </select>
                                @error('cash_account_id')
                                <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div  style="display:none;" class="form-group col-12 col-sm-12 col-md-4 mb-2 bank-way  "
                                 >
                                <label for="account_id"><b>Bank Account</b><span class="text-danger">*</span><span class="text-info " id="balance"
                                                                                                                  ></span></label>
                                <select  style="display:none;" name="account_id" id="account_id" class="form-control" onchange="getBalance()">
                                    <option value="" selected>-- Select Bank Account --</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}" @if($bankAccount){{$bankAccount->id == $data->account_id ? 'selected' : ''}}@endif>{{ $bankAccount->name }}
                                            | {{ $bankAccount->account_number }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('account_id'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('account_id') }}
                                    </span>
                                @endif
                            </div>
                            <div  style="display:none;" class="form-group col-12 col-sm-12 col-md-4 mb-2 bank-way "
                                >
                                <label for="cheque_number"><b>Cheque Number / Transaction</b><span class="text-danger">*</span></label>
                                <input  style="display:none;" type="text" name="cheque_number" id="cheque_number"
                                       class="form-control" value="{{ $data->cheque_number}}" placeholder=" ...">
                                @if ($errors->has('cheque_number'))
                                    <span class="alert text-danger">
                                        {{ $errors->first('cheque_number') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-sm btn-primary mr-2">Update Salary</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>

        $(document).ready(function () {
            // $('#cash_account_id').hide();
            // $('#account_id').hide();
            // $('#cheque_number').hide();
            // $('.bank-way').hide();
            // $('.cash').hide();
            if($('#transaction_way').val() == 1){
                $('.cash').show();
                $('#cash_account_id').show();
            }
            if($('#transaction_way').val() == 2){
                $('.bank-way').show();
                $('#cheque_number').show();
                $('#account_id').show();
            }
        })
// ======================
        $(document).change(function () {
            // $('#cash_account_id').hide();
            // $('#account_id').hide();
            // $('#cheque_number').hide();
            // $('.bank-way').hide();
            // $('.cash').hide();
            if($('#transaction_way').val() == 1){
                $('#account_id').hide();
                $('#cheque_number').hide();
                $('.bank-way').hide();
                $('.cash').show();
                $('#cash_account_id').show();
            }
            if($('#transaction_way').val() == 2){
                $('#cash_account_id').hide();
                $('.cash').hide();
                $('.bank-way').show();
                $('#cheque_number').show();
                $('#account_id').show();
            }
        })


        $(document).ready(function() {
            $('.dropify').dropify();
        });
        ref('expense_by_id');
        function ref(params){
            $('#'+params).select2({
                height:30,
                ajax: {
                    url: '{{route('admin.expense.employee.search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        console.log();
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
        }
        CKEDITOR.replace('note', {
            toolbarGroups: [
                {"name": "styles", "groups": ["styles"]},
                {"name": "basicstyles", "groups": ["basicstyles"]},
                {"name": "paragraph", "groups": ["list", "blocks"]},
                {"name": "document", "groups": ["mode"]},
                {"name": "links", "groups": ["links"]},
                {"name": "insert", "groups": ["insert"]},
                {"name": "undo", "groups": ["undo"]},
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Source,Image,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

        // $(document).on("click", "#add-transaction", function () {
        //     if ($('#add-transaction').is(":checked"))
        //         $(".transaction").show();
        //     else
        //         $(".transaction").hide();
        //     $(".bank-way").hide();
        //     $("#transaction_way").val('');
        //     $("#balance").hide();
        // });


        // function expenseTransactionWay() {
        //     var transaction_way = $("#transaction_way").val();
        //     if (transaction_way == 2) {
        //         $('.bank-way').show();
        //         $('#cash_account_id').val('');
        //         $('.cash').hide();
        //     } else if(transaction_way == 1) {
        //         $('.bank-way').hide();
        //         $('#account_id').val('');
        //         $('.cash').show();
        //     }
        //     else{
        //         $('.bank-way').hide();
        //         $('.cash').hide();
        //         $('#account_id').val('');
        //     }
        // };

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
                    //checkAmount(resp);
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
    </script>
@endpush

