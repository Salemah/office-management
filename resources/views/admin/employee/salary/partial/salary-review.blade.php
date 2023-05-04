<style>
    .text-right {
        text-align: right;
    }
</style>
<div class="body flex-grow-1 px-3">
    <div class="container-lg">
        <!-- Alert -->

        <form action="{{route('admin.salary.update',$employee->id)}}" enctype="multipart/form-data" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <input type="hidden" name="employee_id" value="{{$employee->id}}">
                        <div class="col-sm-6 ">
                            <label for="salary_basic"><b>Basic Salary</b> <span class="text-danger">*</span> </label>
                            <?php
                            if ($employee->current_salary == null){
                                $salary = $employee->joining_salary;
                            }else{
                                $salary = $employee->current_salary;
                            }
                            ?>
                            <input type="number" id="salary_basic" name="basic_salary" class="form-control" value="{{$salary}}" onkeyup="check_data()">
                            @error('salary_basic')
                            <span class="alert text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="new_salary"><b>New Salary</b> <span class="text-danger">*</span></label>
                            <input type="number" id="new_salary" name="new_salary" class="form-control">
                            @error('new_salary')
                            <span class="alert text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12 mb-2">
                            <label for="description"><b>Description </b></label>
                            <textarea name="description" id="description5" rows="3"
                                      class="form-control description"
                                      placeholder="Description..."></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                    </div>
                </div>
            </div>

        </form>
        <div class="mb-5"></div>

    </div>
</div>
