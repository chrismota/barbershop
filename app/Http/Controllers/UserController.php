<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\AdminUserResource;
use App\Services\UserService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        return ApiResponse::success($this->userService->getAllUsers($perPage), 'Users retrieved successfully');
    }

    public function show($id)
    {
        $user = $this->userService->getUser($id);

        return ApiResponse::success(new AdminUserResource($user), 'User retrieved successfully');
    }

    public function showLoggedUser()
    {
        $user = $this->userService->getUser(Auth::id());

        return ApiResponse::success(new AdminUserResource($user), 'User retrieved successfully');
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        return ApiResponse::success(new AdminUserResource($user), 'User created successfully', 201);
    }

    public function update(UpdateUserRequest $request, $userId)
    {
        $user = $this->userService->updateUser($request->validated(), $userId);

        return ApiResponse::success(new AdminUserResource($user), 'User updated successfully');
    }

    public function updateLoggedUser(UpdateUserRequest $request)
    {
        $user = $this->userService->updateUser($request->validated(), Auth::id());

        return ApiResponse::success(new AdminUserResource($user), 'User updated successfully');
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);

        return response()->json(null, 204);
    }

    public function destroyLoggedUser()
    {
        $this->userService->deleteUser(Auth::id());

        return response()->json(null, 204);
    }
}
