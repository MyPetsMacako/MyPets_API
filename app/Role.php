<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table ='roles';
    protected $filliable = ['name'];

    public function register($request)
    {
        $user = new self();
        $user->name = $request->name;
        $user->save();

        return response()->json([
            "message" => 'Rol añadido correctamente'
        ],200);
    }
}
