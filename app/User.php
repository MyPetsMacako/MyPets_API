<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class User extends Model
{
    protected $table ='users';
    protected $filliable = ['name', 'surname', 'email', 'password'];

    public function register(Request $request)
    {
        $user = new self();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->password = encrypt($request->password);
        $user->role_id = 2;
        $user->save();

        return $user->email;
    }

}
