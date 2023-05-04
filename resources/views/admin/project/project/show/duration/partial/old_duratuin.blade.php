
<h5 class="text-primary text-center @if ($project_duration == 0) text-primary @else text-danger @endif" >@if ($project_duration == 0) Initial @else Extends @endif</h5>
<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.duration.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <input type="hidden" name="project_id" value="{{$project->id}}">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="start_date"> <b>Project Start Date </b><span class="text-danger">*</span></label>
            <input type="date"  id="start_date" value="{{ old('start_date')}}" class="form-control " name="start_date"
                   placeholder="Enter Start Date">
            @error('start_date')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
             </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="end_date"><b>Project End Date</b><span class="text-danger">*</span></label>
            <input type="date" id="end_date" value="{{ old('end_date')}}" class="form-control " name="end_date"   placeholder="Enter End Date" onchange="calculateDay()">
            @error('end_date')
               <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        {{-- <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
            <label><b>Duration Type</b></label>
            <select class="form-control" id="duration_type" name="duration_type"onchange="estimateDaySelect()">
                <option value="" selected>--Select--</option>
                <option value="1">Project</option>
                <option value="2">Module</option>
            </select>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-12 mb-2 module_duration"  style="display: none">
            <label for="module_name"><b>Module Name</b><span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('module_name') is-invalid @enderror" name="module_name[]" id="module_name" placeholder="Enter Module Name" >
            @error('module_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration"  style="display: none">
            <label for="module_start_date"> <b>Module Start Date </b><span class="text-danger">*</span></label>
            <input type="date"  id="module_start_date_0" value="{{ old('module_start_date')}}" class="form-control " name="module_start_date[]"
                   placeholder="Enter Start Date" onchange="moduleCheckDate(0)" >
            @error('module_start_date')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
             </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration"  style="display: none">
            <label for="module_end_date"><b>Module End Date</b><span class="text-danger">*</span></label>
            <input type="date"  id="module_end_date_0" value="{{ old('module_end_date')}}" class="form-control " name="module_end_date[]"   placeholder="Enter End Date" onchange="moduleCheckDate(0)">
            @error('module_end_date')
               <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration"  style="display: none">
            <label for="module_estimate_day"><b>Module Estimate Day</b><span class="text-danger">*</span></label>
            <input type="text"  id="module_estimate_day_0" value="{{ old('module_estimate_day')}}" class="form-control " name="module_estimate_day[]"   placeholder="Enter Estimate Day" onchange="moduleCheckDate(0)">
            @error('module_estimate_day')
               <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-5 mb-2 module_duration"  style="display: none">
            <label for="module_estimate_hour"><b>Module Estimate Hour</b><span class="text-danger">*</span></label>
            <input type="text"  id="module_estimate_hour_0" value="{{ old('module_estimate_hour')}}" class="form-control " name="module_estimate_hour[]"   placeholder="Enter Estimate Day" onchange="moduleCheckDate(0)">
            @error('module_estimate_hour')
               <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="col-md-1  module_duration" id="add-module" style="display: none" >
            <button type="button" style="margin-top:22px"  class=" btn  btn-success ">
                +
            </button>
        </div>
        <hr style="color:rgb(0, 0, 0);height:2px;width:98%;margin:auto;display: none" class="my-3 module_duration">
        <div class="module-row" id="">

        </div> --}}
        {{-- <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >

        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration"  style="display: none"  >
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
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration"  style="display: none"  >
            <label for="module_total_hour"><b>Module Total Hour</b><span class="text-danger">*</span></label>
            <input type="num" class="form-control @error('module_total_hour') is-invalid @enderror" name="module_total_hour" id="module_total_hour" placeholder="0.0" readonly>
            @error('module_total_hour')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div> --}}

            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 " >
                <label for="total_day"><b>Estimated Day</b><span class="text-danger">*</span></label>
                <input type="num" class="form-control @error('total_day') is-invalid @enderror" name="total_day" value="{{ old('total_day')}}" id="total_day" placeholder="Enter Total Day"  min="1" onkeyup="getTotalHour()" readonly>
                @error('total_day')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
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
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >
                <label for="estimated_hour"><b>Estimated Hour</b><span class="text-danger">*</span></label>
                <input type="num" class="form-control @error('estimated_hour') is-invalid @enderror" name="estimated_hour" id="estimated_hour" placeholder="0.0" readonly>
                @error('estimated_hour')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            {{-- Adjustment --}}
            <div class="row my-2 "  >
                <div class="col-12 mb-2">
                    <div class="form-group">
                        <label><b>Adjustment</b></label>
                        <input class="form-check-input " type="checkbox" id="adjustment-btn" value="">
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3 mb-2">
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
            </div>
        <div class="form-group my-3">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>

@push('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        function calculateDay(){
            var date1 = new Date($('#start_date').val());
            var date2 = new Date($('#end_date').val());
            if(date2 < date1){
                swal({
                        title: `Please Select Correct Date`,
                        text: date2 + "  Less Than "+ date1,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#end_date').val('');
                        }
                    });
            }
            else{
                var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) +1;
                $('#total_project_days').val(diffDays);
                $('#total_day').val(diffDays);
            }

        }
        function moduleCheckDate(id){
                var date2 = new Date($('#module_start_date_'+id).val());
                var date3 = new Date($('#module_end_date_'+id).val());
            if(date3 < date2){
                swal({
                        title: `Please Select Correct Date`,
                        text: date3 + "  Less Than "+ date2,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_end_date_'+id).val('');
                        }
                    });
            }
            else{
                if(id == 0)
              {
                var date1 = new Date($('#start_date').val());
                var date4 = new Date($('#end_date').val());
                var date2 = new Date($('#module_start_date_'+id).val());
                var date3 = new Date($('#module_end_date_'+id).val());
              }
               else{
                   var previous_id = id -1;
                    var date1 = new Date($('#module_end_date_'+previous_id).val());
                    var date4 = new Date($('#end_date').val());
                    var date2 = new Date($('#module_start_date_'+id  ).val());
                    var date3 = new Date($('#module_end_date_'+id ).val());
               }
                if(date2 < date1){
                        swal({
                        title: `Please Select Correct Date`,
                        text: "Module Start Date  Less Than  Project Start Date.",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_start_date_'+id).val('');
                            $('#module_estimate_day_'+id).val('');
                            $('#module_estimate_hour_'+id).val('');
                        }
                    });
                }
                else if(date3 > date4){
                    swal({
                        title: `Please Select Correct Date`,
                        text: "Module End Date  Greater Than  Project End Date.",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_end_date_'+id).val('');
                            $('#module_estimate_day_'+id).val('');
                            $('#module_estimate_hour_'+id).val('');
                        }
                    });
                }
                else{
                    modulecalculateDay(id);
                }
            }
        }
        function modulecalculateDay(id){
            var date1 = new Date($('#module_start_date_'+id).val());
            var date2 = new Date($('#module_end_date_'+id).val());
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) +1;
            $('#total_module_days').val(diffDays);
            $('#module_estimate_day_'+id).val(diffDays);
            $('#module_estimate_hour_'+id).val(diffDays*8);
            var sum = 0;
            for (let i = 0; i <= id; i++) {
                var day = $('#module_estimate_day_'+i).val();
                var total_calculate_daya = parseInt(sum) + parseInt(day) ;
                var sum = total_calculate_daya;
                $('#module_total_day').val(sum);
                $('#module_total_hour').val(sum * 8);
            }

        }
        //module append
        $(document).ready(function () {
            var wrapper = $(".module-row");
            var x = 0;
            $("#add-module").click(function () {
                x++;
                $(wrapper).append('<div class="row my-3 document-table-tr" id="module-tr-' + x + '">' +
                                        '<div class="form-group col-12 col-sm-12 col-md-12 mb-3 module_duration"  >'+
                                            '<input type="text" class="form-control @error("module_name") is-invalid @enderror" name="module_name[]" id="module_name" placeholder="Enter Module Name" >'+
                                           ' @error("module_name")'+
                                                '<span class="invalid-feedback" role="alert">'+
                                                   ' <strong>{{ $message }}</strong>'+
                                               ' </span>'+
                                            '@enderror'+
                                        '</div>'+
                                       ' <div class="form-group col-12 col-sm-12 col-md-6 mb-3 module_duration"  >'+
                                            '<input type="date"  id="module_start_date_'+x+'" value="{{ old("module_start_date")}}" class="form-control " name="module_start_date[]"placeholder="Enter Start Date" onchange="moduleCheckDate(' + x + ')">'+
                                           ' @error("module_start_date")'+
                                            '<span class="invalid-feedback" role="alert">'+
                                                '<strong>{{ $message }}</strong>'+
                                            '</span>'+
                                            '@enderror'+
                                        '</div>'+
                                        '<div class="form-group col-12 col-sm-12 col-md-6 mb-3 module_duration"  >'+
                                            '<input type="date"  id="module_end_date_'+x+'" value="{{ old("module_end_date")}}" class="form-control " name="module_end_date[]"   placeholder="Enter End Date" onchange="moduleCheckDate('+x+')">'+
                                            '@error("module_end_date")'+
                                            '<span class="invalid-feedback" role="alert">'+
                                                    '<strong>{{ $message }}</strong>'+
                                                '</span>'+
                                            '@enderror'+
                                        '</div>'+
                                        '<div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">'+
                                            '<input type="text"  id="module_estimate_day_'+x+'" value="{{ old("module_estimate_day")}}" class="form-control " name="module_estimate_day[]"   placeholder="Enter Estimate Day" onchange="moduleCheckDate('+x+')">'+
                                            '@error("module_estimate_day")'+
                                            '<span class="invalid-feedback" role="alert">'+
                                                    '<strong>{{ $message }}</strong>'+
                                                '</span>'+
                                            '@enderror'+
                                        '</div>'+
                                        '<div class="form-group col-12 col-sm-12 col-md-5 mb-2 module_duration">'+
                                            '<input type="text"  id="module_estimate_hour_'+x+'" value="{{ old("module_estimate_hour")}}" class="form-control " name="module_estimate_hour[]"   placeholder="Enter Estimate Day" onchange="moduleCheckDate('+x+')">'+
                                            '@error("module_estimate_hour")'+
                                            '<span class="invalid-feedback" role="alert">'+
                                                    '<strong>{{ $message }}</strong>'+
                                                '</span>'+
                                            '@enderror'+
                                        '</div>'+
                                        '<div class="form-group col-sm-1 ">' +
                                            '<button type="button"  class=" btn btn-danger " onclick="moduleRemove(' + x + ')">' +
                                            'X' +
                                            '</button>' +
                                        '</div>'+
                                       '<hr style="color:rgb(0, 0, 0);height:2px;width:98%;margin:auto" class="my-3">'+
                                    '</div>'
                                     );
            });
        });
        function moduleRemove(id) {
            $('#module-tr-' + id).remove();
        }

        function estimateDaySelect() {
            var transaction_way = $("#duration_type").val();
            if (transaction_way == 1) {
                $('.module_duration').hide();
                $('.project_duration').show();

            } else if(transaction_way == 2 ) {
                $('.project_duration').hide();
                $('.module_duration').show();
            }
        };
        $(document).on("click", "#adjustment-btn", function () {
            if ($('#adjustment-btn').is(":checked"))
                $(".adjustment").show();
            else
                $(".adjustment").hide();
                $('#adjustment_hour').val(0);
                getTotalHour() ;
        });
        function getTotalHour() {
            var totalDay = $("#total_day").val();
            var dayHour = $("#estimated_hour_day").val();

            $('#estimated_hour').val(totalDay*dayHour);
            $('#final_hour').val(totalDay*dayHour);
            $('#adjustment_hour').val(0);
            adjustmentHourCount();
        };
        function adjustmentHourCount() {
            var adjustmentType = $('#adjustment_type').val();
            var adjustmentHour = $('#adjustment_hour').val();
            var dayHour = $("#estimated_hour_day").val();
            var totaltHour = $("#total_day").val();
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

    </script>
@endpush
