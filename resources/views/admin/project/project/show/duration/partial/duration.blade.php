<h5 class=" text-center @if ($project_duration == 0) text-primary @else text-danger @endif" >@if ($project_duration == 0) Initial @else Extends <span class="text-warning " style="font-size: 15px"> (Privous End Date : {{$projectDurationEnd->end_date}})</span> @endif</h5>
<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.duration.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <input type="hidden" name="project_id" value="{{$project->id}}">
       <input type="hidden" name="project_end_date_initial" id="project_end_date_initial" @if ($project_duration >0) value="{{$projectDurationEnd->end_date}}" @endif>
       <div class="form-group col-12 col-sm-12 col-md-12 mb-2 module_duration">
        <label for="status"><b>Status</b><span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
            <option value="">--Select Status--</option>
            <option value="1" selected>Up Coming</option>
            <option value="2">On Going</option>
            <option value="3">Complete</option>
            <option value="4">Cancel</option>
            <option value="5">On Hold</option>
        </select>
     </div>
       <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="start_date"> <b>Project Start Date </b><span class="text-danger">*</span></label>
            <input type="date"  id="start_date" value="{{ old('start_date')}}" class="form-control " name="start_date" placeholder="Enter Start Date" @if ($project_duration >0) onchange="chechExtendDate()"  @endif>
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
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 " >
                <label for="duration_total_day"><b>Estimated Day</b><span class="text-danger">*</span></label>
                <input type="num" class="form-control @error('total_day') is-invalid @enderror" name="total_day" value="{{ old('total_day')}}" id="duration_total_day" placeholder="Enter Total Day"  min="1" onkeyup="getTotalDurationHour()" readonly>
                @error('total_day')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">
                <label for="duration_estimated_hour_day"><b>Estimated Hour Per Day</b><span class="text-danger">*</span></label>
                <input type="num" class="form-control @error('estimated_hour_day') is-invalid @enderror" name="estimated_hour_day" id="duration_estimated_hour_day" placeholder="0.0" onkeyup="getTotalDurationHour()">
                @error('estimated_hour_day')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >
                <label for="vacation_day"><b>Vacation</b><span class="text-danger">*</span></label>
                <input type="num" class="form-control @error('vacation_day') is-invalid @enderror" name="vacation_day" id="vacation_day" placeholder="0.0" onkeyup="calculateDay()">
                @error('vacation_day')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >
                <label for="final_day"><b>Total Day</b><span class="text-danger">*</span></label>
                <input type="num" class="form-control @error('final_day') is-invalid @enderror" name="final_day" id="final_day" placeholder="0.0" readonly>
                @error('final_day')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >

            </div>
            <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >
                <label for="estimated_hour"><b>Estimated Hour</b><span class="text-danger">*</span></label>
                <input type="num" class="form-control @error('estimated_hour') is-invalid @enderror" name="estimated_hour" id="duration_estimated_hour" placeholder="0.0" readonly>
                @error('estimated_hour')
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
                <div class="col-12 col-sm-12 col-md-3 mb-2">
                    <div class="form-group adjustment" style="display: none">
                        <label><b>Adjustment Type</b></label>
                        <select class="form-control" id="adjustment_type" name="adjustment_type" onchange="DurationadjustmentHourCount()">
                            <option value="" selected>--Select--</option>
                            <option value="1">Addition</option>
                            <option value="2">Subtraction</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-3 mb-2">
                    <div class="form-group adjustment" style="display: none">
                        <label><b>Adjustment hour</b></label>
                        <input type="number" name="adjustment_hour" id="adjustment_hour"class="form-control " placeholder="0" onkeyup="DurationadjustmentHourCount()">
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
                        <input type="num" class="form-control @error('final_hour') is-invalid @enderror" name="final_hour" id="duration_final_hour" placeholder="0.0" readonly>
                        @error('final_hour')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            <div class="form-group col-12 mb-2">
                <label for="description"><b>Description</b></label>
                <textarea name="description" id="description" rows="10" cols="40" class="form-control description" value="{{ old('description') }}" placeholder="Description..."></textarea>
                @error('description')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        <div class="form-group my-3">
            <button type="submit" class="btn btn-sm btn-primary mb-3">Create</button>
        </div>
    </div>
</form>

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        CKEDITOR.replace(description,{
            toolbarGroups: [{
                "name": "styles",
                "groups": ["styles"]
            },
                {
                    "name": "basicstyles",
                    "groups": ["basicstyles"]
                },

                {
                    "name": "paragraph",
                    "groups": ["list", "blocks"]
                },
                {
                    "name": "document",
                    "groups": ["mode"]
                },
                {
                    "name": "links",
                    "groups": ["links"]
                },
                {
                    "name": "insert",
                    "groups": ["insert"]
                },

                {
                    "name": "undo",
                    "groups": ["undo"]
                },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Image,Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
        })
        function chechExtendDate(){
            var date1 = new Date($('#start_date').val());
            var projectEndDate = new Date($('#project_end_date_initial').val());
            if( date1< projectEndDate){
                $('#start_date').val('');
                swal({
                        title: `Please Select Correct Date`,
                        text: date1 + "  Less Than "+ projectEndDate,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#start_date').val('');
                        }
                    });
            }
            calculateDay();
        }
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
                var vacationDay = $('#vacation_day').val();
                var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) +1;
                $('#duration_total_day').val(diffDays);
                if(diffDays<vacationDay){
                    $('#vacation_day').val('');
                    $('#final_day').val(diffDays);

                    swal({
                        title: `Please Write Valid Vacation`,
                        text: `Vacation Extend Total Project Day`,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#vacation_day').val('');
                        }
                    });
                    getTotalDurationHour();
                }
                else{
                    $('#final_day').val(diffDays - vacationDay);
                    getTotalDurationHour();
                }
            }
        }
        $(document).on("click", "#adjustment-btn", function () {
            if ($('#adjustment-btn').is(":checked"))
                {
                    $(".adjustment").show();
                }
            else
                {
                    $(".adjustment").hide();
                    $('#adjustment_hour').val(0);
                    getTotalDurationHour() ;
                }
        });

        function getTotalDurationHour() {
            var totalDay = $("#final_day").val();
            var dayHour = $("#duration_estimated_hour_day").val();
            $('#duration_estimated_hour').val(totalDay*dayHour);
            $('#duration_final_hour').val(totalDay*dayHour);
           $('#adjustment_hour').val(0);
            DurationadjustmentHourCount();
        };

        function DurationadjustmentHourCount() {
            var adjustmentType = $('#adjustment_type').val();
            var adjustmentHour = $('#adjustment_hour').val();
            var dayHour = $("#duration_estimated_hour_day").val();
            var totaltHour = $("#final_day").val();
            if (adjustmentType == 1) {
                if (adjustmentHour) {
                    var finalHour = (parseFloat(totaltHour)*dayHour) + parseFloat(adjustmentHour);
                    $("#duration_final_hour").val(finalHour);
                }
            } else if (adjustmentType == 2) {
                if (adjustmentHour) {
                    var finalBalance = (parseFloat(totaltHour)*dayHour) - parseFloat(adjustmentHour)
                    $("#duration_final_hour").val(finalBalance);
                }
            }
        }
    </script>
@endpush
