@push('css')
@endpush
<form action="{{ route('admin.loan-return.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
      <input type="hidden" name="loan_id" id="loan_id" value="{{$loan->id}}">
      <input type="hidden" class="form-control" id="loan_date" value="{{$loan->loan_date}}" readonly>
      <input type="hidden" class="form-control" id="interest_rate" value="{{$loan->interest_rate}}" readonly>
      <input type="hidden" class="form-control" id="duration" value="{{$loan->duration}}" readonly>
      <input type="hidden" class="form-control" id="status" value="{{$loan->status}}" readonly>
      <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="return_title"><b>Return Title</b><span class="text-danger">*</span></label>
                <input type="text" name="return_title" id="return_title"class="form-control"value="{{ old('return_title') }}" placeholder="Enter Return Title...">
                @if ($errors->has('return_title'))
                    <span class="alert text-danger">
                        {{ $errors->first('return_title') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="transaction_date"><b>Date</b><span class="text-danger">*</span></label>
                <input type="date" name="transaction_date" id="transaction_date"class="form-control" value="{{ old('transaction_date') }}" onchange="checkInterest()">
                @if ($errors->has('transaction_date'))
                    <span class="alert text-danger">
                        {{ $errors->first('transaction_date') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="loan_author_id"><b>Select Authority</b><span class="text-danger">*</span></label>
                <input type="text" class="form-control" value="{{ $loan->author->name }}"readonly>
                <input type="hidden" class="form-control" id="loan_author_id" name="loan_author_id"value="{{ $loan->author->id }}">
                @if ($errors->has('loan_author_id'))
                    <span class="alert text-danger">
                      {{ $errors->first('loan_author_id') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="loan_amount"><b>Loan amount</b><span class="text-danger">*</span></label>
            <input type="text" name="" id="loan_amount" class="form-control" readonly value="{{ $loan_amount }}">
                @if ($errors->has('loan_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('loan_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="return_amount"><b>Return amount</b><span class="text-danger">*</span></label>
            <input type="text" name="" id="return_amount" class="form-control" readonly value="{{ $return_amount }}">
                @if ($errors->has('return_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('return_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-4 mb-2">
            <label for="investment_due_amount"><b>Due amount</b><span class="text-danger">*</span><span class="text-danger" id="interest">(Interest : {{round($interest_till_today, 2)}})</span> </label>
            <input  type="text" name="investment_due_amount" id="investment_due_amount" class="form-control" readonly value="{{ $due }}">
                @if ($errors->has('investment_due_amount'))
                    <span class="alert text-danger">
                      {{ $errors->first('investment_due_amount') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="loan_type"><b>Loan Type</b><span class="text-danger">*</span></label>
            <input type="text" class="form-control"
                    @if ($loan->loan_type == 2) value="Giving"
                    @else value="Taking" @endif readonly>
             <input type="hidden" class="form-control" id="loan_type" name="loan_type" value="{{ $loan->loan_type }}">
                @if ($errors->has('loan_type'))
                    <span class="alert text-danger">
                      {{ $errors->first('loan_type') }}
                    </span>
                @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="transaction_way"><b>Transaction Type</b><span class="text-danger">*</span></label>
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
            <input type="text" name="cheque_number" id="cheque_number"
                    class="form-control" value="{{ old('cheque_number') }}" placeholder=" ...">
            @if ($errors->has('cheque_number'))
                <span class="alert text-danger">
                    {{ $errors->first('cheque_number') }}
                </span>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="amount_balance"><b>Return Amount</b><span class="text-danger">*</span><span class="text-info" id="balance" style="display: none"></span></label>
            <input type="hidden" value="" id="amount_balance">
            <input type="number" step="any" name="amount" id="amount" class="form-control"value="{{ old('amount') }}" placeholder="1200 ..." onkeyup="checkAmount(this)">
            @if ($errors->has('amount'))
                <span class="alert text-danger">
                    {{ $errors->first('amount') }}
                </span>
            @endif
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label for="note"><b>Note</b></label>
            <textarea name="note" id="description" rows="3" class="form-control " value="{{ old('note') }}"placeholder="Enter Note..."></textarea>
                @if ($errors->has('note'))
                    <span class="alert text-danger">
                       {{ $errors->first('note') }}
                    </span>
                @endif
        </div>
            <div class="form-group" @if ($due == 0) style="display:none"  @endif>
                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            </div>


    </div>

</form>



@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js" integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.js"></script>
    <script>
        CKEDITOR.replace('description', {
            height:'100px',
            toolbarGroups: [
                { "name": "styles","groups": ["styles"] },
                { "name": "basicstyles","groups": ["basicstyles"] },
                { "name": "paragraph","groups": ["list", "blocks"] },
                { "name": "document","groups": ["mode"] },
                { "name": "links","groups": ["links"] },
                { "name": "insert","groups": ["insert"] },
                { "name": "undo","groups": ["undo"] },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Image,Source,contact_person_primary_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        });

    function checkInterest(){
       var loanDate = $('#loan_date').val();
        var returnDate = $('#transaction_date').val();
        var interestRate = $('#interest_rate').val();
        var duration = $('#duration').val();
        var status = $('#status').val();
        var loanAmount = $('#loan_amount').val();
        var returnAmount = $('#return_amount').val();

        var startdate = new Date(loanDate);
        var enddate = new Date(returnDate);

        var months = (enddate.getMonth() - startdate.getMonth()) +(12 * (enddate.getFullYear() - startdate.getFullYear()));
        var yearly_interst =  (parseFloat(loanAmount) *parseFloat(interestRate)) / 100;
        var monthly_interest =  yearly_interst / 12;

        var interest_till_today= monthly_interest *months;

        var due = (parseFloat(loanAmount) + parseFloat(interest_till_today))-parseFloat(returnAmount);
        $('#investment_due_amount').val(due);
        document.getElementById('interest').innerHTML = ' (Interest : ' + interest_till_today.toFixed(2) + ' )';

        var year = diff_years(startdate, enddate);

        if(status == 1 && year != 0){

                let p = loanAmount;
                let t = year;
                let r =interestRate;

                let CI = parseFloat(p) * (Math.pow((1 + parseFloat(r) / 100), parseFloat(t)));
                let  dueCi = (parseFloat(CI)-parseFloat(returnAmount));
                interest_till_today_ci = CI - p;
                document.getElementById('interest').innerHTML = ' (Interest : ' + interest_till_today_ci.toFixed(2) + ' )';
                $('#investment_due_amount').val(parseFloat(dueCi));
        }
    }
    function diff_years(dt2, dt1)
    {
        var diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= (60 * 60 * 24);
        return Math.abs(Math.round(diff/365.25));
    }


    $('#transaction_way').on('change', function () {
            $('#amount').val(0);
    })
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
    $('#amount').on('keyup', function () {
            if ($('#transaction_way').val()) {
                if($('#transaction_way').val() == 1){
                    checkAmount($('#amount').val());
                }else{
                    if($('#account_id').val()){
                        checkAmount($('#amount').val());
                    }else{
                        $('#amount').val(0);
                        alert('please select account');
                    }
                }

            } else {
                $('#amount').val(0);
                alert('please select transaction type first');
            }
    })
    function checkAmount(amount) {
            console.log($('#transaction_purpose').val());
            var amount = amount;
            var amountBalance = 0;
            var amountBalanceX = parseFloat($('#amount_balance').val()); //accblnce
            var invest_amount_balance = parseFloat($('#investment_due_amount').val()); //invst blnce

            if ($('#transaction_purpose').val() != 11) {
                if (!isNaN(amountBalanceX) && !isNaN(invest_amount_balance)) {
                    if (parseFloat(amountBalanceX) > parseFloat(invest_amount_balance)) {
                        amountBalance = invest_amount_balance;
                    } else {
                        amountBalance = amountBalanceX;
                    }

                    if (parseFloat(amountBalance) < parseFloat(amount)) {
                        swal({
                            title: `Alert?`,
                            text: "You have entered a wrong balance.",
                            buttons: true,
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                $('#amount').val(0);
                            }
                        });
                    }
                }
            } else {
                if (parseFloat(amountBalanceX) < parseFloat(amount)) {
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
    }
    </script>
@endpush
