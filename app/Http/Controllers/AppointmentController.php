<?php

namespace App\Http\Controllers;
use App\Appointment;
use Illuminate\Http\Request;
use App\Helpers\Token;
use App\Pet;
use App\User;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $appointment = Appointment::all();

        $appointmentsData = array();
        $appointmentsData = $appointment->toArray();

        return response()->json(
            $appointmentsData
        ,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();

        $appointment = new Appointment();
        $appointment = $appointment->register($request);

        return response()->json([
            "message" => 'Cita registrada correctamente'
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();

        $user_id = $user->id;

        $appointment = Appointment::where('id', '=', $id)->first();

        $user_id_of_appointment = $appointment->user_id;

        if($user_id!=$user_id_of_appointment)
        {
            return response()->json([
                "message" => 'Solo puedes editar tus citas'
            ], 401);
        }

        if($request->pet_id==NULL || $request->date==NULL ||  $request->title==NULL)
        {
            return response()->json([
                "message" => 'Rellena todos los campos'
            ], 401);
        }

        $pet = Pet::where('user_id', '=', $user_id)->where('id', '=', $request->pet_id)->first();
   
        if($pet != NULL)
        {
            $appointment->pet_id = $request->pet_id;
            $appointment->date = $request->date;
            $appointment->title = $request->title;
            $appointment->save();

            return response()->json([
                "message" => 'Cita actualizada'
            ], 200);
        }else{

            return response()->json([
                "message" => 'Solo puedes editar las citas de tus mascotas'
            ], 401);

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();

        $appointment = Appointment::where('user_id', '=', $user->id)->where('id', '=', $id)->first();
   
        if($appointment!= NULL)
        {
            $appointment->delete();

            return response()->json([
                "message" => 'Cita eliminada correctamente'
            ], 200);

        }else{

            return response()->json([
                "message" => 'Solo puedes eliminar tus citas'
            ], 401);

        }
    }

    public function showAppointmentsByDateOrder(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();

        $appointments = $user->appointments()->orderBy('date', 'asc')->get();

        return response()->json(
            $appointments
        ,200);
    }

    public function showAppointmentDetails(Request $request, $id){

        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();

        $appointment = Appointment::where('user_id', '=', $user->id)->where('id', '=', $id)->first();

        if($appointment!= NULL)
        {
            $pet = $appointment->pet()->first();
        
            return response()->json([
                "pet_name" => $pet->name,
                "title" => $appointment->title,
                "date" => $appointment->date
            ],200);

        }else{

            return response()->json([
                "message" => 'Solo puedes acceder a los detalles de tus citas'
            ], 401);

        }
    }
}
