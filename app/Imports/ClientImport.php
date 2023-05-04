<?php

namespace App\Imports;

use App\Models\CRM\Client\Client;
use App\Models\CRM\Client\ClientContactPeople;
use App\Models\CRM\Client\ClientType;
use App\Models\CRM\Client\ContactThrough;
use App\Models\CRM\Client\InterestedOn;
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


class ClientImport implements ToCollection, WithHeadingRow,SkipsOnError, WithValidation
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
            'email' => 'nullable|sometimes|unique:clients,email',
            'phone_primary' => 'required|unique:clients,phone_primary',
        ];
    }
    // public function customValidationMessages()
    // {
    //     return [
    //         'name.required' => 'Name field is required',
    //         'email' => 'Email must be unique',
    //         'phone_primary.unique' => 'Phone must be unique',

    //     ];
    // }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = new Client();
            $data->name = $row['name'];
            $data->email = $row['email'] ?? '';
            $data->phone_primary = $row['phone_primary'];
            $data->present_address = $row['present_address'] ?? '';

            // $clientType = ClientType::where('name', $row['client_type'])->first();

            // if ($clientType == null) {
            //     $clientType = new ClientType();
            //     $clientType->name = $row['client_type'];
            //     $clientType->priority = $row['client_type_priority'];
            //     $clientType->status = 1;
            //     $clientType->access_id = json_encode(UserRepository::accessId(Auth::id()));
            //     $clientType->created_by = Auth::id();
            //     $clientType->save();
            // }
            // $data->client_type =  $clientType->id;
            // $data->client_type_priority = $clientType->priority;

            // $contactThrough = ContactThrough::where('name', $row['contact_through'])->first();

            // if ($contactThrough == null) {
            //     $contactThrough = new ContactThrough();
            //     $contactThrough->name = $row['contact_through'];
            //     $contactThrough->status = 1;
            //     $contactThrough->access_id = json_encode(UserRepository::accessId(Auth::id()));
            //     $contactThrough->created_by = Auth::id();
            //     $contactThrough->save();
            // }

            // $interestedOn = InterestedOn::where('name', $row['interested_on'])->first();

            // if ($interestedOn == null) {
            //     $interestedOn = new InterestedOn();
            //     $interestedOn->name = $row['interested_on'];
            //     $interestedOn->status = 1;
            //     $interestedOn->access_id = json_encode(UserRepository::accessId(Auth::id()));
            //     $interestedOn->created_by = Auth::id();
            //     $interestedOn->save();
            // }
            // $data->contact_through = $contactThrough->id;
            // $data->interested_on = $interestedOn->id;
            $data->status = 1;
            $data->created_by = Auth::user()->id;
            $data->assign_to = json_encode([]);
            $data->own_assign = json_encode([Auth::id()]);
            $data->is_assign = 0 ;
            $data->access_id = json_encode(UserRepository::accessId(Auth::id()));
            $data->save();
            $contactPersons = $row['contact_person'];
            $contactPersonemails = $row['contact_person_email'];
            $contactPersonPhones = $row['contact_person_phone'];
            $contactPersonDesignations = $row['contact_person_designation'];
            $contactDateOfBirths = $row['contact_person_date_of_birth'];

            $contactPerson = explode (",", $contactPersons);
            $contactPersonemail = explode (",", $contactPersonemails);
            $contactPersonPhone = explode (",", $contactPersonPhones);
            $contactPersonDesignation = explode (",", $contactPersonDesignations);
            $contactDateOfBirth = explode (",", $contactDateOfBirths);

            foreach($contactPerson as $key => $contact_person){
                $contact_person = new ClientContactPeople();
                $contact_person->client_id =  $data->id;
                $contact_person->name = $contactPerson[$key]?? '';
                $contact_person->email = $contactPersonemail[$key]?? '';
                $contact_person->phone = $contactPersonPhone[$key]?? '';
                $contact_person->designation = $contactPersonDesignation[$key] ?? '';
                $contact_person->date_of_birth = $contactDateOfBirth[$key]?? '';
                $contact_person->created_by = Auth::user()->id;
                $contact_person->access_id = json_encode(UserRepository::accessId(Auth::id()));
                $contact_person->save();
            }



            // $user = new User();
            // $user->name = $data->name;
            // // $user->email = $data->email;
            // $user->mobile =  $data->phone_primary;
            // $user->password = Hash::make('client');
            // $user->user_id = $data->id;
            // $user->user_type = 2;
            // $user->record_access = 1;
            // $user->role_id = 2;
            // $user->access_id = json_encode(UserRepository::accessId(Auth::id()));
            // $user->created_by = Auth::id();
            // $user->save();
        }
        //}

    }
}
