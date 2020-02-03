<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Appointment extends Model
{
    protected $table ='appointments';
    protected $filliable = ['pet_id', 'date', 'description'];

    public function register(Request $request)
    {
        $appointment = new self();
        $appointment->pet_id = $request->pet_id;
        $appointment->date = $request->date;
        $appointment->description = $request->description;
        $appointment->save();

        return $appointment;
    }
}
