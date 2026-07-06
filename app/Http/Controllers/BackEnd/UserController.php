<?php

namespace App\Http\Controllers\BackEnd;

use App\DataTables\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\BackEnd\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(
        protected UserService $userService
    ) {}

    public function main_user(UsersDataTable $dataTable)
    {
        return $dataTable->render('BackEnd.auth.content.user');
    }

    public function user_entry(Request $request)
    {
        return $this->userService->main_user($request);
    }

    public function user_store(StoreUserRequest $request)
    {
        return $this->userService->user_store($request);
    }

    public function user_cpass(Request $request)
    {
        return $this->userService->user_cpass($request);
    }

    public function user_upass(UpdatePasswordRequest $request)
    {
        return $this->userService->user_upass($request);
    }

    public function user_ustat(Request $request)
    {
        return $this->userService->user_ustat($request);
    }
    public function user_destroy(Request $request)
    {
        return $this->userService->user_destroy($request);
    }
}
