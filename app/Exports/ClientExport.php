<?php

namespace App\Exports;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientType;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientExport implements FromCollection,WithMapping,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;

    public function map($member): array
    {
        if(isset($member->interestedOn)){
            $interestedOn = $member->interestedOn->name;
          }
          else{
              $interestedOn = "--";
          }
          if(isset($member->contactThrough))
          {
            $contactThrough = $member->contactThrough->name;
          }
          else{
              $contactThrough = "--";
          }
          if(isset($member->clientType))
          {
            $clientType =  $member->clientType->name;
          }
          else{
              $clientType = "--";
          }
        return [
            $member->id,
            $member->name,
            $member->email,
            $member->phone_primary,
            $member->phone_secondary,

            $interestedOn,
            $contactThrough ,
            $clientType,
            $member->client_type_priority,
            $member->present_address,
            $member->permanent_address,
            $member->description,
        ];
    }
    public function collection()
    {
        return Client::with('interestedOn','contactThrough','clientType')->get();
    }
    public function headings(): array
    {
        return [
                'Id',
                'Name',
                'Email',
                'Phone Primary',
                'Phone Secondary',
                'Interested On',
                'Contact Through',
                'Client Type',
                'Client Priority',
                'Present Address',
                'Permanent Address',
                'Country',
                'Description',

        ];
    }
}
