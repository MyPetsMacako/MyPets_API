<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class User extends Model
{
    protected $table ='users';
    protected $filliable = ['fullname', 'nickname', 'email', 'password', 'photo', 'tel_number'];

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function appointments()
    {
        return $this->hasManyThrough('App\Appointment','App\Pet');
    }

    public function register(Request $request)
    {
        $user = new self();
        $user->fullname = $request->fullname;
        $user->nickname = $request->nickname;
        $user->email = $request->email;
        $user->password = encrypt($request->password);
        $user->isBanned = false;
        $user->role_id = 2;
        $user->save();

        return $user->email;
    }

}
