<?php
namespace App\Imports;

use App\Models\Employee\Department;
use App\Models\Employee\Designation;
use App\Models\Employee\Employee;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;


class EmployeeImport implements ToCollection, WithHeadingRow,SkipsOnError, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    use Importable, SkipsErrors;
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'phone_primary' => 'required|unique:users,mobile',
            'employee_id' => 'required|unique:employees,employee_id',
            'designation' => 'required',
            'department' => 'required',
            'joining_date' => 'required',

        ];
    }
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Name field is required',
            'email.required' => 'Email field is required',
            'email.unique' => 'Email field must be unique',

        ];
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = new Employee();
            $data->name = $row['name'];
            $data->email = $row['email'];
            $data->employee_id= $row['employee_id'];
            $data->phone_primary = $row['phone_primary'];
            $department = Department::where('name', $row['department'])->first();

            if ($department == null) {
                $department = new Department();
                $department->name = $row['department'];
                $department->status = 1;
                $department->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $department->created_by = Auth::id();
                $department->save();
            }
            $data->department =  $department->id;

            $designation = Designation::where('name', $row['designation'])->first();

            if ($designation == null) {
                $designation = new Designation();
                $designation->name = $row['designation'];
                $designation->status = 1;
                $designation->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $designation->created_by = Auth::id();
                $designation->save();
            }

            $data->designation = $designation->id;
            $data->created_by = Auth::user()->id;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();

            $user = new User();
            $user->name = $data->name;
            $user->email = $data->email;
            $user->mobile =  $data->phone_primary;
            $user->password = Hash::make('employee');
            $user->user_id = $data->id;
            $user->user_type = 1;
            $user->record_access = 1;
            $user->role_id = 3;
            $user->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $user->created_by = Auth::id();
            $user->save();
        }
        //}

    }
}

