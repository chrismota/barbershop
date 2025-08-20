<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    public function getAllUsers($perPage = 10): LengthAwarePaginator
    {
        $userType = UserType::where('role', 'admin')->first();

        if (!$userType) {
            throw new NotFoundHttpException("User type admin not found.");
        }

        return User::where('user_type_id', $userType->id)->paginate($perPage);
    }

    public function getUser($userId): User
    {
        $user = User::find($userId);

        if (!$user) {
            throw new NotFoundHttpException("User not found.");
        }

        return $user;
    }

    public function createUser(array $userData): User
    {
        $userType = UserType::where('role', 'admin')->first();

        if (!$userType) {
            throw new NotFoundHttpException("User type admin not found.");
        }

        return User::create([
            'name'         => $userData['name'],
            'email'        => $userData['email'],
            'password'     => $userData['password'],
            'user_type_id' => $userType->id,
        ]);
    }

    public function updateUser(array $userData, $userId): User
    {
        $user = User::find($userId);

        if (!$user) {
            throw new NotFoundHttpException("User not found.");
        }

        $user->update($userData);

        return $user;
    }

    public function deleteUser($userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            throw new NotFoundHttpException("User not found.");
        }

        return $user->delete();
    }
}
