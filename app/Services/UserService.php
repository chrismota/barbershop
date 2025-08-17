<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    public function getAllUsers(): array
    {
        return User::all()->toArray();
    }

    public function getUser($userId): User
    {
        $user = User::find($userId);

        if (!$user) {
            throw new NotFoundHttpException("Usuário não encontrado.");
        }

        return $user;
    }

    public function createUser(array $userData): User
    {
        $userType = UserType::where('role', $userData['role'])->first();

        if (!$userType) {
            throw new NotFoundHttpException("User type '{$userData['role']}' não encontrado.");
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
            throw new NotFoundHttpException("Usuário não encontrado.");
        }

        $user->update($userData);

        return $user;
    }

    public function deleteUser($userId): bool
    {
        $user = User::find($userId);

        if (!$user) {
            throw new NotFoundHttpException("Usuário não encontrado.");
        }

        return $user->delete();
    }
}
