<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Models\User;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function getAllUsers(): Collection
    {
        $users = User::all();

        if($users->isEmpty()) {
            throw new ResourceNotFoundException('No users found.');
        }

        if($users->every->trashed()) {
            throw new ResourceNotFoundException('Users have all been deleted.');
        }

        return $users;
    }

    public function getUser(int $id): User
    {
        $user = User::find($id);

        if(is_null($user)) {
            throw new ResourceNotFoundException('User not found.');
        }

        if($user->trashed()) {
            throw new ResourceNotFoundException('User does not exist.');
        }

        return $user;
    }
}