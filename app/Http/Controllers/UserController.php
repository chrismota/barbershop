<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return response()->json($this->userService->getAllUsers(), 200);
    }

    public function show($id)
    {
        $user = $this->userService->getUser($id);

        return response()->json($user->toArray(), 200);
    }

    public function showLoggedUser()
    {
        $user = $this->userService->getUser(Auth::id());

        return response()->json($user->toArray(), 200);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json($user->toArray(), 201);
    }

    public function update(UpdateUserRequest $request, $userId)
    {
        $user = $this->userService->updateUser($request->validated(), $userId);

        return response()->json($user->toArray(), 200);
    }

    public function updateLoggedUser(UpdateUserRequest $request)
    {
        $user = $this->userService->updateUser($request->validated(), Auth::id());

        return response()->json($user->toArray(), 200);
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'message' => 'Admin deleted successfully',
        ], 204);
    }

    public function destroyLoggedUser()
    {
        $this->userService->deleteUser(Auth::id());

        return response()->json([
            'message' => 'Admin deleted successfully',
        ], 204);
    }
}
