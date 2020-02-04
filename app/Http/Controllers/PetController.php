<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pet;
use App\User;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pets = Pet::all();

        $usersData = array();
        $usersData = $pets->toArray();

        return response()->json(
            $usersData
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
        $pet = new Pet();
        $pet = $pet->register($request);

        return response()->json([
            "message" => 'Mascota registrada correctamente'
        ],200);
    }

    public function adminStore(Request $request)
    {
        $pet = new Pet();
        $pet = $pet->adminRegister($request);

        if ($pet == "error"){
            return response()->json([
                "message" => 'El id del usuario introcucido no existe'
            ],401);
        } else {
            return response()->json([
                "message" => 'Mascota registrada correctamente'
            ],200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $user_email = ['email' => $request->email];
        
        $user = User::where($user_email)->first();

        return response()->json([
            $user->pets,
        ], 200);
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

        $user_email = ['email' => $request->email];
        
        $request_user = User::where($user_email)->first();

        $user_id = $request_user->id;

        $pet = Pet::where('id', '=', $id)->first();

        $user_id_of_pet = $pet->user_id;

        if($user_id!=$user_id_of_pet)
        {
            return response()->json([
                "message" => 'Solo puedes editar tus mascotas'
            ], 401);
        }

        if($request->name==NULL || $request->species==NULL ||  $request->breed==NULL || $request->colour==NULL || $request->weight==NULL || $request->birth_date ==NULL)
        {
            return response()->json([
                "message" => 'Rellena todos los campos'
            ], 401);
        }

        $pet->name = $request->name;
        $pet->species = $request->species;
        $pet->breed = $request->breed;
        $pet->colour = $request->colour;
        $pet->weight = $request->weight;
        $pet->birth_date = $request->birth_date;
        $pet->save();

        return response()->json([
            "message" => 'Mascota actualizada'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pet = Pet::find($id);
        $pet->delete();

        return response()->json([
            "message" => 'Mascota eliminada correctamente'
        ],200);
    }
}
