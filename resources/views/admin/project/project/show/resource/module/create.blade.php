<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.module.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <input type="hidden" name="project_id" value="{{$project->id}}">
        @if ($projectDuration)
            <input type="hidden" name="project_duration_id" value="{{$projectDuration->id}}">
            <input type="hidden" name="project_start_date" id="project_start_date" value="{{$projectDuration->start_date}}">
            <input type="hidden" name="project_end_date" id="project_end_date" value="{{$projectDuration->end_date}}">
        @endif


        <div class="form-group col-12 col-sm-12 col-md-12 mb-2 module_duration"  >
            <label for="module_name"><b>Module Name</b><span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('module_name') is-invalid @enderror" name="module_name" id="module_name" placeholder="Enter Module Name" >
            @error('module_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
            <label for="module_start_date"> <b>Module Start Date </b><span class="text-danger">*</span></label>
            <input type="date"  id="module_start_date" value="{{ old('module_start_date')}}" class="form-control " name="module_start_date"placeholder="Enter Start Date" onchange="moduleCheckDate()" >
            @error('module_start_date')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
             </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
            <label for="module_end_date"><b>Module End Date</b><span class="text-danger">*</span></label>
            <input type="date"  id="module_end_date" value="{{ old('module_end_date')}}" class="form-control " name="module_end_date" placeholder="Enter End Date" onchange="moduleCheckDate()">
            @error('module_end_date')
               <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
            <input type="hidden"  id="module_estimate_day" value="{{ old('module_estimate_day')}}" class="form-control " name="module_estimate_day"   placeholder="Enter Estimate Day" onchange="moduleCheckDate()" readonly>
            <label for="status"><b>Status</b><span class="text-danger">*</span></label>
            <select name="status" id="status"class="form-select @error('status') is-invalid @enderror">
                <option value="">--Select Status--</option>
                <option value="1" selected>Up Coming</option>
                <option value="2">On Going</option>
                <option value="3">Complete</option>
                <option value="4">Cancel</option>
                <option value="5">On Hold</option>
            </select>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">
            <label for="estimated_hour_day"><b>Estimated Hour Per Day</b><span class="text-danger">*</span></label>
            <input type="num" class="form-control @error('estimated_hour_day') is-invalid @enderror" name="estimated_hour_day" id="estimated_hour_day" placeholder="0.0" onkeyup="getTotalHour()">
            @error('estimated_hour_day')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >

        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration"    >
            <label for="module_total_day"><b>Module Total Day</b><span class="text-danger">*</span></label>
            <input type="num" class="form-control @error('module_total_day') is-invalid @enderror" name="module_total_day" id="module_total_day" placeholder="0.0" readonly>
            @error('module_total_day')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >

        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration"    >
            <label for="module_total_hour"><b>Module Total Hour</b><span class="text-danger">*</span></label>
            <input type="num" class="form-control @error('module_total_hour') is-invalid @enderror" name="module_total_hour" id="module_total_hour" placeholder="0.0" readonly>
            @error('module_total_hour')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
            {{-- Adjustment --}}

                <div class="col-12 mb-2">
                    <div class="form-group">
                        <label><b>Adjustment</b></label>
                        <input class="form-check-input " type="checkbox" id="adjustment-btn" value="">
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="form-group adjustment" style="display: none">
                        <label><b>Adjustment Type</b></label>
                        <select class="form-control" id="adjustment_type" name="adjustment_type" onchange="adjustmentHourCount()">
                            <option value="" selected>--Select--</option>
                            <option value="1">Addition</option>
                            <option value="2">Subtraction</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3 mb-2">
                    <div class="form-group adjustment" style="display: none">
                        <label><b>Adjustment hour</b></label>
                        <input type="number" name="adjustment_hour" id="adjustment_hour"class="form-control " placeholder="0" onkeyup="adjustmentHourCount()">
                        @error('adjustment_hour')
                        <span class="text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 mb-2" >
                    <div class="form-group">
                        <label for="final_hour"><b>Final Hour</b><span class="text-danger">*</span></label>
                        <input type="num" class="form-control @error('final_hour') is-invalid @enderror" name="final_hour" id="final_hour" placeholder="0.0" readonly>
                        @error('final_hour')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

        <div class="form-group my-3">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>
@push('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
     function moduleCheckDate(){
                var date1 = new Date($('#module_start_date').val());
                var date2 = new Date($('#module_end_date').val());
                var date3 = new Date($('#project_start_date').val());
                var date4 = new Date($('#project_end_date').val());
            if(date1 < date3){
                $('#module_start_date').val('');
                swal({
                        title: `Please Select Correct Date`,
                        text: date1 + "  Less Than "+ date3,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_start_date').val('');
                        }
                    });
            }
            else if(date2 > date4){
                    $('#module_end_date').val('');
                    $('#module_estimate_day').val('');
                    $('#module_estimate_hour').val('');
                    $('#final_hour').val('');
                    $('#module_total_hour').val('');
                    $('#estimated_hour_day').val('');
                    $('#module_total_day').val('');
                    swal({
                        title: `Please Select Correct Date`,
                        text:  date2 +"Greater Than "+date4,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_end_date').val('');
                            $('#module_estimate_day').val('');
                            $('#module_estimate_hour').val('');
                        }
                    });
                }
                else{
                    modulecalculateDay();
                }

        }
        function modulecalculateDay(id){
            var date1 = new Date($('#module_start_date').val());
            var date2 = new Date($('#module_end_date').val());
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) +1;
            $('#module_estimate_day').val(diffDays);
            $('#module_total_day').val(diffDays);
            $('#module_estimate_hour').val(diffDays*8);
            getTotalHour();
        }
        function getTotalHour() {
            var totalDay = $("#module_estimate_day").val();
            var dayHour = $("#estimated_hour_day").val();

            $('#estimated_hour').val(totalDay*dayHour);
            $('#module_total_hour').val(totalDay*dayHour);
            $('#final_hour').val(totalDay*dayHour);
            $('#adjustment_hour').val(0);
            adjustmentHourCount();
        };
        function adjustmentHourCount() {
            var adjustmentType = $('#adjustment_type').val();
            var adjustmentHour = $('#adjustment_hour').val();
            var dayHour = $("#estimated_hour_day").val();
            var totaltHour = $("#module_total_day").val();
            if (adjustmentType == 1) {
                if (adjustmentHour) {
                    var finalHour = (parseFloat(totaltHour)*dayHour) + parseFloat(adjustmentHour);
                    $("#final_hour").val(finalHour);
                }
            } else if (adjustmentType == 2) {
                if (adjustmentHour) {
                    var finalBalance = (parseFloat(totaltHour)*dayHour) - parseFloat(adjustmentHour)
                    $("#final_hour").val(finalBalance);
                }
            }
        }
    $(document).on("click", "#adjustment-btn", function () {
            if ($('#adjustment-btn').is(":checked"))
                $(".adjustment").show();
            else
                $(".adjustment").hide();
                $('#adjustment_hour').val(0);
                getTotalHour() ;
    });
</script>
@endpush
