<form action="{{route('admin.hrm.paid-leave-update')}}" id="" method="POST">
    @csrf
    {{-- @method('put') --}}
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <p id="#upmsgcontainer"></p>
                <input type="hidden" name="paid_leave_id" id="all_id" value="{{$data->id}}">
                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                    <label for="days"><b>Days Per Year</b><span class="text-danger">*</span></label>
                    <input type="text" name="days" id="days"
                        class="form-control @error('days') is-invalid @enderror"
                        value="{{ $data->days }}" placeholder="Enter Days">
                    @error('days')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                    <label for="paid_per_day"><b>Paid Per Day</b><span class="text-danger">*</span></label>
                    <input type="text" name="paid_per_day" id="paid_per_day"
                        class="form-control @error('paid_per_day') is-invalid @enderror"
                        value="{{ $data->paid_per_day }}" placeholder="Paid Per Day">
                    @error('paid_per_day')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
                    <label for="total_amount"><b>Total Amount</b><span class="text-danger">*</span></label>
                    <input readonly type="text" name="total_amount" id="total_amount"
                        class="form-control @error('total_amount') is-invalid @enderror"
                        value="{{ $data->total_amount }}" placeholder="Total Amount">
                    @error('total_amount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-12 mb-2">
                    <label for="description"><b>Description</b></label>
                    <textarea name="description" id="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror" value="{{ $data->description }}"
                        placeholder="Description..."></textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                </div>
            </div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
      $("#paid_per_day").keyup(function(){
       var paid_per_day = $("#paid_per_day").val();
       var days = $("#days").val();
       var totalAmount = parseFloat(paid_per_day) * parseFloat(days)
       $("#total_amount").val(totalAmount);
      });
    });
    </script>