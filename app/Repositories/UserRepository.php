<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository
{
    public static function accessId($id)
    {
        try {
            $parentId = [$id];
            if ($id !== 1) {
                $createdBy = User::findOrFail($id)->created_by;
                do {
                    $parent = User::findOrFail($createdBy);
                    $createdBy = $parent->created_by;
                    $parentId [] = $parent->id;
                } while ($createdBy !== 1);

                $parentId [] = 1;
            }
            return $parentId;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }
}
