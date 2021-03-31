<?php

namespace App\Http\Controllers\User;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    public function index(Request $request)
    {
        return $this->showAll(User::where('id', '>', 1)->get());
    }

    public function store(Request $request)
    {
        $user = new User($request->all());
        $user->password = bcrypt($user->password);
        $user->save();
        return $this->showOne($user);
    }

    public function show(User $user)
    {
        return $this->showOne($user);
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'email' => 'string',
            'name' => 'string',
        ];

        $old_password = $user->password;

        $this->validate($request, $rules);
        $user->fill($request->only([
            'email',
            'password',
            'type',
            'name',
            'configuration',
        ]));

        DB::beginTransaction();
        try {
            $new_id = array();
            if (!is_null($user->password)) {
                $user->password = bcrypt($user->password);
            } else {
                $user->password = $old_password;
            }
            $user->save();
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollBack();
        }
        DB::commit();

        return $this->showOne($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user);
    }

    public function getUserLogged()
    {
        $idUser = Auth::id();
        return $this->showOne(User::findOrFail($idUser));
    }
}
