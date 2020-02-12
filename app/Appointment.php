<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Helpers\Token;

class Appointment extends Model
{
    protected $table ='appointments';
    protected $filliable = ['pet_id', 'date', 'title'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function register(Request $request)
    {
        $request_token = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($request_token);
        $user_email = $decoded_token->email;
        $user = User::where('email', '=', $user_email)->first();
        $user_id = $user->id;

        $appointment = new self();
        $appointment->user_id = $user_id;
        $appointment->pet_id = $request->pet_id;
        $appointment->date = $request->date;
        $appointment->title = $request->title;
        $appointment->save();

        return $appointment;
    }
}
