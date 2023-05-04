<?php

namespace App\Exports;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientType;
use App\Models\Employee\Employee;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromCollection,WithMapping,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function map($member): array
    {
        return [
            $member->id,
            $member->employee_id,
            $member->name,
            $member->email,
            $member->phone_primary,
            $member->phone_secondary,
            $member->departments->name,
            $member->designations->name,
            $member->present_address,
            $member->permanent_address,
            $member->description,
        ];
    }
    public function collection()
    {
        return Employee::with('departments', 'designations')->get();
    }
    public function headings(): array
    {
        return [
                'Id',
                'Employee Id',
                'Name',
                'Email',
                'Phone Primary',
                'Phone Secondary',
                'Departments',
                'Designations',
                'Present Address',
                'Permanent Address',
                'Country',
                'Description',

        ];
    }
}
