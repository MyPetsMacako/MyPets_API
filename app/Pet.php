<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Helpers\Token;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class Pet extends Model
{
    protected $table ='pets';
    protected $filliable = ['user_id', 'name', 'species', 'breed', 'weight', 'birth_date', 'color', 'photo', 'documents'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }


    /*public function register(Request $request)
    {
        $request_token = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($request_token);
        $user_email = $decoded_token->email;
        $user = User::where('email', '=', $user_email)->first();
        $user_id = $user->id;

        $pet = new self();
        $pet->user_id = $user_id;
        $pet->name = $request->name;
        $pet->species = $request->species;
        $pet->breed = $request->breed;
        $pet->colour = $request->colour;
        $pet->weight = $request->weight;
        $pet->birth_date = $request->birth_date;
        $pet->save();
    }*/

// Al final dejar solo un metodo si funciona correctamente enviar el user_id como null en ios

public function register(Request $request)
    {
        if (($request->user_id) == NULL) {
            $request_token = $request->header('Authorization');
            $token = new token();
            $decoded_token = $token->decode($request_token);
            $user_email = $decoded_token->email;
            $user = User::where('email', '=', $user_email)->first();
            $user_id = $user->id;

            $pet = new self();
            $pet->user_id = $user_id;
            $pet->name = $request->name;
            $pet->species = $request->species;
            $pet->breed = $request->breed;
            $pet->color = $request->color;
            if ($request->image != NULL)
            {
                $photo = Storage::putFileAs('Pets', new File($request->image), "$user->id$pet->name.jpg");
                $pet->photo = $photo;
            }
            $pet->weight = $request->weight;
            $pet->birth_date = $request->birth_date;
            $pet->save();

        } else {

            $user = User::where('id', '=', $request->user_id)->first();

            if ($user == NULL) {
                return $status = "error";
            } else {
                $pet = new self();
                $pet->user_id = $request->user_id;
                $pet->name = $request->name;
                $pet->species = $request->species;
                $pet->breed = $request->breed;
                $pet->color = $request->color;
                $pet->weight = $request->weight;
                $pet->birth_date = $request->birth_date;
                $pet->save();
            }
        }
    }
}
