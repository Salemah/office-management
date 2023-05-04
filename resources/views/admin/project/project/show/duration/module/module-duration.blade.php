@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <style>
        .select2-container--default .select2-selection--single{
           padding:6px;
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
            integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });
    </script>
@endpush

<form class="add-client-document" enctype="multipart/form-data"action="{{ route('admin.project.duration.store') }}"method="POST">
                    @csrf
                    <div class="row">
                            <input type="hidden" name="project_id" id="project_id" value="{{$project->id}}">
                            <input type="hidden" name="project_duration_id" id="project_duration_id" value="">
                            <input type="hidden" name="project_start_date" id="project_start_date"@if (isset($projectStartDate)) value="{{$projectDurationInitial->start_date}}" @endif>
                            <input type="hidden" name="project_end_date" id="project_end_date" @if (isset($projectInitial)) value="{{$projectInitial->end_date}}"@endif  >
                        <div class="form-group col-12 col-sm-12 col-md-12 mb-2">
                             <label for="module_id"><b>Module Name</b></label>
                             <select name="module_id" id="moduleNamed"class="form-control select2" >
                                 <option>--Select Module--</option>
                             </select>
                             @error('module_id')
                                 <span class="invalid-feedback" role="alert">
                                     <strong>{{ $message }}</strong>
                                 </span>
                             @enderror
                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="module_start_date"> <b>Module Start Date </b><span class="text-danger">*</span></label>
                            <input type="date" id="module_start_date" value="{{ old('start_date')}}"class="form-control " name="start_date" placeholder="Enter Start Date"onchange="moduleCheckDate()">
                            @error('start_date')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="module_end_date"><b>Module End Date</b><span class="text-danger">*</span></label>
                            <input type="date" id="module_end_date" value="{{ old('end_date')}}" class="form-control @error('end_date') is-invalid @enderror" name="end_date" placeholder="Enter End Date" onchange="moduleCheckDate()">
                            @error('end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <input type="hidden" id="module_estimate_day" value="{{ old('module_estimate_day')}}"class="form-control " name="estimate_day" placeholder="Enter Estimate Day"onchange="moduleCheckDate()" readonly>
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
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">
                            <label for="estimated_hour_day"><b>Estimated Hour Per Day</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('estimated_hour_day') is-invalid @enderror" name="estimated_hour_day" id="estimated_hour_day" placeholder="0.0" onkeyup="getTotalHourModule()">
                            @error('estimated_hour_day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">
                            <label for="module_vacation_day"><b>Vacation Day</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('total_day') is-invalid @enderror" name="vacation_day" id="module_vacation_day" placeholder="0.0" onkeyup="modulecalculateDay()">
                            @error('total_day')
                              <span class="invalid-feedback" role="alert">
                                   <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >
                            <label for="module_total_day"><b>Module Total Day</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('total_day') is-invalid @enderror" name="total_day" id="module_total_day" placeholder="0.0" readonly>
                            @error('total_day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >

                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="final_day"><b>Total Day</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('final_day') is-invalid @enderror" name="final_day" id="module_final_day" placeholder="0.0" readonly>
                            @error('final_day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">

                         </div>
                         <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="module_total_hour"><b>Module Total Hour</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('module_total_hour') is-invalid @enderror" name="estimated_hour" id="module_total_hour" placeholder="0.0" readonly>
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
                                <input class="form-check-input " type="checkbox" id="module_adjustment-btn" value="">
                            </div>
                         </div>
                         <div class="col-md-3 mb-2">
                            <div class="form-group adjustment" style="display: none">
                                <label><b>Adjustment Type</b></label>
                                <select class="form-control" id="module_adjustment_type" name="adjustment_type" onchange="moduleAdjustmentHourCount()">
                                    <option value="" selected>--Select--</option>
                                    <option value="1">Addition</option>
                                    <option value="2">Subtraction</option>
                                </select>
                            </div>
                         </div>
                         <div class="col-12 col-sm-12 col-md-3 mb-2">
                            <div class="form-group adjustment" style="display: none">
                                <label><b>Adjustment hour</b></label>
                                <input type="number" name="adjustment_hour" id="module_adjustment_hour" class="form-control " placeholder="0" onkeyup="moduleAdjustmentHourCount()">
                                @error('adjustment_hour')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                         </div>
                         <div class="col-12 col-sm-12 col-md-6 mb-2">
                            <div class="form-group">
                                <label for="final_hour_module"><b>Final Hour</b><span class="text-danger">*</span></label>
                                <input type="num" class="form-control @error('final_hour_module') is-invalid @enderror" name="final_hour" id="final_hour_module" placeholder="0.0" readonly>
                                @error('final_hour_module')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                         </div>
                         <div class="form-group col-12 mb-2">
                              <label for="description"><b>Description</b></label>
                              <textarea name="description" id="module_description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Description..."></textarea>
                              @error('description')
                              <span class="invalid-feedback" role="alert">
                                   <strong>{{ $message }}</strong>
                              </span>
                              @enderror
                         </div>
                         <div class="form-group my-3">
                            <button type="submit" class="btn btn-sm btn-primary mb-3 ">Create</button>
                         </div>
                    </div>
</form>
@push('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
      CKEDITOR.replace(module_description,{
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
            removeButtons: 'Image,Source,contact_person_phone,Strike,Subscript,Superscript,Anchor,Styles,Specialchar,PasteFromWord'
     })
        $('#moduleNamed').select2({
                ajax: {
                    url: '{{route('admin.project.module.search.module')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            project : $('#project_id').val(),
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
                                    text: item.project_title,
                                    value: item.id,
                                    id: item.id,
                                }
                            })
                        };
                    }
                }
        });

        function moduleCheckDate() {
                var date1 = new Date($('#module_start_date').val());
                var date2 = new Date($('#module_end_date').val());
                var date3 = new Date($('#project_start_date').val());
                var date4 = new Date($('#project_end_date').val());
                if (date1 < date3) {
                    $('#module_start_date').val('');
                    swal({
                        title: `Please Select Correct Date`,
                        text: date1 + "  Less Than " + date3,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_start_date').val('');
                        }
                    });
                }
                else if (date1 > date4) {
                    $('#module_start_date').val('');
                    swal({
                        title: `Please Select Correct Date`,
                        text: date1 + " Greater Than " + date4,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_start_date').val('');
                        }
                    });

                }
                else if (date2 > date4) {
                    $('#module_end_date').val('');
                    $('#module_estimate_day').val('');
                    $('#module_estimate_hour').val('');
                    $('#final_hour_module').val('');
                    $('#module_total_hour').val('');
                    $('#estimated_hour_day').val('');
                    $('#module_total_day').val('');
                    swal({
                        title: `Please Select Correct Date`,
                        text: date2 + "Greater Than " + date4,
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
                else if (date2 < date1) {
                    $('#module_end_date').val('');
                    swal({
                        title: `Please Select Correct Date`,
                        text: date2 + "  Less Than " + date1,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_end_date').val('');
                        }
                    });
                }
                else if (date2 < date3) {
                    $('#module_end_date').val('');
                    swal({
                        title: `Please Select Correct Date`,
                        text: date2 + "  Less Than " + date1,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_end_date').val('');
                        }
                    });
                }
                else {
                    modulecalculateDay();
                }

        }

        function modulecalculateDay(id) {
            var vacationDay = $('#module_vacation_day').val();
            var date1 = new Date($('#module_start_date').val());
            var date2 = new Date($('#module_end_date').val());
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) + 1;
            if(diffDays<vacationDay){
                    $('#module_vacation_day').val('');
                    $('#module_final_day').val(diffDays);

                    swal({
                        title: `Please Write Valid Vacation`,
                        text: `Vacation Extend Total Project Day`,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_vacation_day').val('');
                        }
                    });
                    getTotalHourModule();
                }
                else{
                    $('#module_estimate_day').val(diffDays);
                    $('#module_final_day').val(diffDays - vacationDay);
                    $('#module_total_day').val(diffDays);
                    $('#module_estimate_hour').val(diffDays - vacationDay* 8);
                    getTotalHourModule();
                }
                getTotalHourModule();
        }

        function getTotalHourModule() {
            var totalDay = $("#module_final_day").val();
            var dayHour = $("#estimated_hour_day").val();

            //$('#estimated_hour').val(totalDay * dayHour);
            $('#module_total_hour').val(totalDay * dayHour);
            $('#final_hour_module').val(totalDay * dayHour);
            $('#module_adjustment_hour').val(0);
            moduleAdjustmentHourCount();
        };

        function moduleAdjustmentHourCount() {
            var adjustmentType = $('#module_adjustment_type').val();
            var adjustmentHour = $('#module_adjustment_hour').val();
            var dayHour = $("#estimated_hour_day").val();
            var totaltHour = $("#module_final_day").val();
            if (adjustmentType == 1) {
                if (adjustmentHour) {
                    var finalHour = (parseFloat(totaltHour) * dayHour) + parseFloat(adjustmentHour);
                    $("#final_hour_module").val(finalHour);
                }
            } else if (adjustmentType == 2) {
                if (adjustmentHour) {
                    var finalBalance = (parseFloat(totaltHour) * dayHour) - parseFloat(adjustmentHour)
                    $("#final_hour_module").val(finalBalance);
                }
            }
        }

        $(document).on("click", "#module_adjustment-btn", function () {
            if ($('#module_adjustment-btn').is(":checked"))
                $(".adjustment").show();
            else
                $(".adjustment").hide();
                $('#module_adjustment_hour').val(0);
            getTotalHourModule();
        });

</script>
@endpush
