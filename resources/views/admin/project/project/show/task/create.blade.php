<form class="add-client-document" enctype="multipart/form-data" action="{{ route('admin.project.task.store') }}"
      method="POST">
    @csrf
    <div class="row">
        <input type="hidden" id="project_id" name="project_id" value="{{$project->id}}">
        <input type="hidden" id="project_start_date" name="project_start_date" @if ($projectDurationInitial) value="{{$projectDurationInitial->start_date}}" @endif >
        <input type="hidden" id="project_end_date" name="project_end_date" @if($projectDuration)  value="{{$projectDuration->end_date}}"@endif>

        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >
            <label for="task_type"><b>Task Type</b><span class="text-danger">*</span></label>
            <select name="task_type" id="task_type"class="form-select @error('task_type') is-invalid @enderror" onchange="taskTypeCheck()">
                <option value="" >--Select Task--</option>
                <option value="1"selected >Project</option>
                 <option value="2" @if (!$module>0) disabled @endif>Module</option>
            </select>
            @error('task_type')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "   >
            <div class="module" style="display: none">
                <label for="module_id"><b>Module Name</b><span class="text-danger">*</span></label>
            <select name="module_id" id="module_id"class="form-select @error('module_id') is-invalid @enderror">
                <option value="">--Select Task--</option>
                <option value="" >Module</option>
            </select>
            @error('module_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">
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
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="assign_employee_id"><b>Assign To</b><span class="text-danger">*</span></label>
            <select name="assign_employee_id[]" id="assign_employee_id" class="form-select @error('assign_employee_id') is-invalid @enderror select2" multiple="multiple">
                <option value="" >--Select Employee--</option>
            </select>
            @error('assign_employee_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
            <label for="start_date"> <b>Start Date </b><span class="text-danger">*</span></label>
            <input type="date" id="start_date" value="{{ old('start_date')}}"class="form-control @error('start_date') is-invalid @enderror" name="start_date" placeholder="Enter Start Date"onchange="moduleCheckDate()">
            @error('start_date')
                <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
            <label for="end_date"><b>End Date</b><span class="text-danger">*</span></label>
            <input type="date" id="end_date" value="{{ old('end_date')}}" class="form-control @error('end_date') is-invalid @enderror" name="end_date" placeholder="Enter End Date" onchange="moduleCheckDate()">
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
            {{-- <div class=""> --}}
                <div class="col-12 col-sm-6 col-md-6 mb-2" >
                    <div class="form-group">
                        <label for="document_title"><b>Document Title</b></label>
                        <input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" >
                        @error('document_title')
                            <span class="text-danger" role="alert">
                                <p>{{ $message }}</p>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-sm-5 col-md-5 mb-2" >
                    <div class="form-group">
                        <label for="document"><b>Document</b></label>
                        <input data-height="25"class="dropify form-control " type="file" placeholder="Document" name="documents[]" value="">
                        @error('documents')
                            <span class="text-danger" role="alert">
                                <p>{{ $message }}</p>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class=" col-12 col-sm-1 col-md-1" >
                    <button type="button" style="margin-top:25px"  class=" btn btn-sm btn-danger " disabled>
                        X
                    </button>
                </div>
                <div class="documentRow document">

                </div>
                <div class="col-sm-4 " >
                    <button type="button" style="margin-top:0px" id="add_document"
                            class="btn btn-sm btn-success text-white">
                        + Add Document
                    </button>
                </div>
            {{-- </div> --}}
        <div class="form-group col-12 mb-2">
            <label for="description"><b>Description</b></label>
            <textarea name="description" id="description" rows="3"
                      class="form-control @error('description') is-invalid @enderror"
                       placeholder="Description..."></textarea>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"
        integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
        CKEDITOR.replace(description,{
            height: '110px',
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
        });

        function taskTypeCheck(){
            let type = $('#task_type').val();
             if(type == 2){
                $('.project').hide();
                $('.module').show();
            }
            else{
                $('.project').hide();
                $('.module').hide();
            }
        }

        $('#module_id').select2({
            ajax:  {
                        url: '{{route('admin.project.module.search.module')}}',
                        dataType: 'json',
                        type: "POST",
                        data: function (params) {
                            var query = {
                                project: $('#project_id').val(),
                                search: params.term,
                                type: 'public'
                            }
                            return query;
                        },
                        processResults: function (data) {
                            console.log(data);
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

        $('#assign_employee_id').select2({
               placeholder:"--Select Reporting Person--",
                ajax: {
                    url: '{{route('admin.project.reporting.search')}}',
                    dataType: 'json',
                    type: "POST",
                    data: function (params) {
                        var query = {
                            project: $('#project_id').val(),
                            search: params.term,
                            type: 'public'
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
        function moduleCheckDate() {
            var date1 = new Date($('#start_date').val());
            var date2 = new Date($('#end_date').val());
            var date3 = new Date($('#project_start_date').val());
            var date4 = new Date($('#project_end_date').val());
            if(date1 < date3){
                $('#start_date').val('');
                $('#end_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: "Task Start Date Cann't Less than Project Duration Date" ,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#module_start_date').val('');
                    }
                });
            }
           else if(date1 > date4){
                $('#start_date').val('');
                $('#end_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: "Task Start Date Cann't Grater  than Project End Date" ,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#module_start_date').val('');
                    }
                });
            }
            else if(date4<date2){
                $('#start_date').val('');
                $('#end_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: "Task End Date Cann't Greater than Project End Date" ,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#module_start_date').val('');
                    }
                });
            }
            else if(date2<date3 ){
                $('#start_date').val('');
                $('#end_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: date2 +"less than" +  date3,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#module_start_date').val('');
                    }
                });
            }
            else if(date2<date1 ){
                $('#start_date').val('');
                $('#end_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: date2 + " less than " + date1 ,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#module_start_date').val('');
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
          //document append
          $(document).ready(function () {
            var wrapper = $(".documentRow");
            var x = 0;
            $("#add_document").click(function () {
                x++;
                $(wrapper).append('<div class="row mt-2 document-table-tr" id="document-table-tr-' + x + '">' +
                                '<div class="col-sm-6 mb-2 document">'+
                                        ' <div class="form-group">'+
                                            '<input class="form-control " type="text" placeholder="Document Title.." name="document_title[]" >'+
                                            '@error("document_title")'+
                                                '<span class="text-danger" role="alert">'+
                                                    ' <p>{{ $message }}</p>'+
                                                '</span>'+
                                            ' @enderror'+
                                        '</div>'+
                                    ' </div>'+
                                    '<div class="col-sm-5 mb-2">'+
                                            '<div class="form-group">'+
                                                '<input data-height="25"class="dropify form-control" type="file" placeholder="Document" name="documents[]" value="">'+
                                            '</div>'+
                                        '</div>'+
                                        '<div class="col-sm-1 ">' +
                                            '<button type="button"  class=" btn btn-sm btn-danger " onclick="documentRemove(' + x + ')">' +
                                            'X' +
                                            '</button>' +
                                        '</div>'+
                                    '</div>' );
                                    $('.dropify').dropify();

            });
        });
        function documentRemove(id) {
            $('#document-table-tr-' + id).remove();
        }
</script>
@endpush
