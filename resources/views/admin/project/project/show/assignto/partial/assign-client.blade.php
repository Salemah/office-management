<form  action="{{ route('admin.project.assign-to.store') }}"
    method="POST">
    @csrf
    <div class="row">
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <div class="form-group">
                <label for="department_id"><b>Department</b><span class="text-danger">*</span></label>
                <select name="department_id[]" id="department_id"class="form-control department_id" multiple="multiple">
                    <option value="0" id="all-department">All Department</option>
                    @forelse ($departments as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @empty
                        <option>No Department</option>
                    @endforelse
                </select>
                @error('department_id')
                <span class="text-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="form-group col-12 col-sm-12 col-md-6 mb-2">
            <label for="employee_id"><b>Assign To</b><span class="text-danger">*</span></label>
            <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror">
                <option value="" >--Select Employee--</option>
            </select>
            @error('employee_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <input type="hidden" name="project_id" id="project_id" value="{{ $project->id }}">
        <div class="form-group">
            <button type="submit" id="submit-btn" class="btn btn-sm btn-primary mb-3">Assign</button>
        </div>
    </div>
</form>

@push('script')
    <script>
$('#department_id').select2({
            placeholder: 'Select Department',
        });
        $('#employee_id').select2({
            placeholder: 'Select Employee',
        });
        // dept
        $('#department_id').on('change', function() {
            changeEmployee();
            let value = $(this).val();
            if(value.includes("0")){
                $(this).empty();
                $(this).append('<option selected value="0">All Department</option>');
            }
            if(value == ''){
                changeDepartment();
            }

        });
        //employee
        $("#employee_id").on('change', function (){
            let value = $(this).val();
            if(value.includes("0")){
                $(this).empty();
                if($('#department_id').val() == 0)
                {$(this).append('<option selected value="0">All Employee</option>');}
            }
           if(value == ''){
               changeEmployee();
           }
        })

        function changeEmployee(){
            let department_id = $('#department_id').val();
            $("#employee_id").empty();
            $.ajax({
                url: "{{ route('admin.project.employee.search') }}",
                type: 'post',
                data: { departmentId: department_id},
                success: function(response) {
                    if($('#department_id').val() == 0)
                    {$("#employee_id").append('<option value="0">All Employee</option>');}
                    $.each(response, function(key, value) {
                        console.log(value.id)
                        $("#employee_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        }
        function changeDepartment(){
            let department_id = $('#department_id').val();
            $("#employee_id").empty();
            $.ajax({
                url: "{{ route('admin.hrm.getDepartment') }}",
                type: 'post',
                data: { departmentId: department_id},
                success: function(response) {
                    $.each(response, function(key, value) {
                        console.log(value.id)
                        $("#department_id").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                }
            });
        }
    </script>
@endpush
