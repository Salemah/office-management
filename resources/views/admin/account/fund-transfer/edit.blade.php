@extends('layouts.dashboard.app')

@section('title', 'Edit Fund-Transfer')
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link
        rel="stylesheet"href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            padding: 6px;
            height: 37px;
            width: 100%;
            font-size: 1.2em;
            position: relative;
        }
    </style>
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                <a href="{{ route('admin.account.fund-transfer.index') }}">Fund-Transfer</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.account.fund-transfer.edit', $fundTransfer->id) }}">Edit</a>
            </li>
        </ol>
        <a href="{{ route('admin.account.fund-transfer.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')
    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.account.fund-transfer.update', $fundTransfer->id) }}" enctype="multipart/form-data"
        method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="warehouse_id"><b>Warehouse</b><span class="text-danger">*</span></label>
                                @php
                                    $auth = Auth::user();
                                     $user_role = $auth->roles->first();
                                @endphp
                                @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin' )
                                    <select name="warehouse_id" id="warehouse_id"class="form-select @error('warehouse_id') is-invalid @enderror">
                                        <option value="" selected >--Select warehouse_id--</option>
                                        @forelse ($warehouses as $item)
                                            <option value="{{ $item->id }}" {{$item->id == $transaction->warehouse_id ? 'selected' : ''}} >
                                                {{ $item->name }}
                                            </option>
                                        @empty
                                            <option value="">No Warehouse</option>
                                        @endforelse
                                    </select>

                                @else
                                <input type="text" name="warehouse_id_name" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="{{$mystore->name}}" readonly >
                                <input type="hidden" name="warehouse_id" id="warehouse_id" class="form-control" placeholder="Enter Invoice No" value="{{$mystore->id}}" >
                                @endif
                                @error('warehouse_id')
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="fund_transaction_title"><b>Fund Transfer Title</b><span
                                        class="text-danger">*</span></label>
                                <input type="text" name="fund_transaction_title"
                                    id="fund_transaction_title"class="form-control"
                                    value="{{ $transaction->transaction_title }}"placeholder="Enter Title...">
                                @if ($errors->has('fund_transaction_title'))
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $errors->first('fund_transaction_title') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="fund_transaction_date"><b>Date</b><span class="text-danger">*</span></label>
                                <input type="date" name="fund_transaction_date"
                                    id="fund_transaction_date"class="form-control"
                                    value="{{ $transaction->transaction_date }}">
                                @if ($errors->has('fund_transaction_date'))
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $errors->first('fund_transaction_date') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="form_account_id"><b>Account From</b><span class="text-danger">*</span></label>
                                <select name="form_account_id" id="form_account_id"
                                    class="form-control"onchange="getBalance()">
                                    <option value="" selected>-- Select Bank Account --</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}"
                                            @if ($bankAccount->id == $fundTransfer->cash_out_account) selected @endif>{{ $bankAccount->name }} |
                                            {{ $bankAccount->account_number }}
                                            @if ($bankAccount->type == 1)
                                                | Cash-Account
                                            @else
                                                | Bank-Account
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('form_account_id'))
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $errors->first('form_account_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="to_account_id"><b>Account To</b><span class="text-danger">*</span></label>
                                <select name="to_account_id" id="to_account_id" class="form-control">
                                    <option value="" selected>-- Select Bank Account --</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}"
                                            @if ($bankAccount->id == $fundTransfer->cash_in_account) selected @endif>{{ $bankAccount->name }}|
                                            {{ $bankAccount->account_number }}@if ($bankAccount->type == 1)
                                                | Cash-Account
                                            @else
                                                | Bank-Account
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('to_account_id'))
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $errors->first('to_account_id') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="deposit_source"><b>Amount</b><span class="text-danger">*</span>
                                    <span class="text-info" id="balance" style="display: none">*</span><span
                                        id="balanceHide">( <span class="text-info">{{ $accountBalance }}</span>
                                        )</span></label>
                                <input type="hidden" value="{{ $accountBalance }}" id="amount_balance">

                                <input type="number" min="0" name="amount" id="amount" class="form-control"
                                    value="{{ $fundTransfer->amount }}" placeholder="1200 ..." onkeyup="checkAmount(this)">
                                @if ($errors->has('amount'))
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $errors->first('amount') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                                <label for="cheque_number"><b>Cheque Number</b></label>
                                <input type="text" name="cheque_number" id="cheque_number"
                                    class="form-control"value="{{ $transaction->cheque_number }}" placeholder=" ...">
                                @if ($errors->has('cheque_number'))
                                    <span class="alert text-danger" role="alert">
                                        <strong>{{ $errors->first('cheque_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group col-12 mb-2">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" id="description" rows="3"
                                    class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}"
                                    placeholder="Description...">{{ $transaction->description }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <div class="mb-5"></div>


@endsection
@push('script')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $('#warehouse_id').on("change", function() {
            $('#form_account_id').val('').trigger('change');
            $('#to_account_id').val('').trigger('change');
        });


        $('#form_account_id').select2({
            ajax: {
                url: '{{ route('admin.account.fund-transfer.bank.account.search') }}',
                type: "POST",
                dataType: 'json',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                    }
                    return query;
                },
                processResults: function(data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function(item) {
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
        $('#to_account_id').select2({
            ajax: {
                url: '{{ route('admin.account.fund-transfer.bank.account.search') }}',
                type: "POST",
                dataType: 'json',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        warehouse_id: $('#warehouse_id').val(),
                    }
                    return query;
                },
                processResults: function(data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: $.map(data, function(item) {
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

        function getBalance() {
            var accountId = $('#form_account_id').val();
            $('#balanceHide').hide();
            if (accountId !== null) {
                var url = '{{ route('admin.account.bank.account.balance', ':id') }}';
                $.ajax({
                    type: "GET",
                    url: url.replace(':id', accountId),
                    success: function(resp) {
                        $('#balance').show();
                        document.getElementById('balance').innerHTML = '( ' + resp + ' )';
                        $('#amount_balance').val(resp);
                        document.getElementById('amount').max = resp;
                    }, // success end
                    error: function(error) {
                        // location.reload();
                    } // Error
                })
            } else {

            }
        }

        function getPurpose() {
            if ($('#transaction_purpose').val() == 2) {
                $('#balance').hide();
            } else {
                var accountId = $('#account_id').val();
                getBalance(accountId)
            }
        }

        function checkAmount(amount) {
            var amount = amount.value;
            var amountBalance = $('#amount_balance').val();
            if (parseFloat(amountBalance) < parseFloat(amount)) {
                swal({
                    title: `Alert?`,
                    text: "You don't have enough balance.",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#amount').val(0);
                    }
                });
            }
        }
    </script>
@endpush
