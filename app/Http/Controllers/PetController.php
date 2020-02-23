<?php

namespace App\Http\Controllers;
use App\Helpers\Token;
use Illuminate\Http\Request;
use App\Pet;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class PetController extends Controller
{

    public function adminRequestedPetInfo($id)
    {
        $pet = Pet::select("id", "name", "species", "breed", "weight", "color", "birth_date")->where("id", "=", $id)->first();
    
        return response()->json(
            $pet
        ,200);
    }

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
    /*public function store(Request $request)
    {
        $pet = new Pet();
        $pet = $pet->register($request);

        return response()->json([
            "message" => 'Mascota registrada correctamente'
        ],200);
    }*/

    public function store(Request $request)
    {
        $pet = new Pet();
        $pet = $pet->register($request);

        if ($pet == "error")
        {
            return response()->json([
                "message" => 'El id del usuario introcucido no existe'
            ],401);
        }
        else
        {
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
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();
        $path = 'http://localhost:8888/laravel-ivanodp/MyPets_API/storage/app/';
        $pets = Pet::where('user_id', $user->id)->get();
        $ids = array();
        $names = array();
        $breeds = array();
        $weights = array();
        $colors = array();
        $birth_dates = array();
        $images = array();
        if (isset($pets)){
            foreach ($pets as $key => $pet) {
                array_push($ids, $pet->id);
                array_push($names, $pet->name);
                array_push($breeds, $pet->breed);
                array_push($weights, $pet->weight);
                array_push($colors, $pet->color);
                array_push($birth_dates, $pet->birth_date);
                $file = "$path" .   $pet->photo;
                array_push($images, $file);
            }
        }

        return response()->json(
            ["ids"=>$ids, "names"=>$names, "breeds"=>$breeds,"weights"=>$weights,"colors"=>$colors,"birth_dates"=>$birth_dates, "images" => $images]
        , 200);
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
        $pet->photo = $request->photo;
        $pet->documents = $request->documents;
        $pet->save();

        return response()->json([
            "message" => 'Mascota actualizada'
        ], 200);
    }

    public function updatePetsForAdmin(Request $request, $id)
    {
        $pet = Pet::where('id', '=', $id)->first();

        if($request->name==NULL || $request->species==NULL ||  $request->breed==NULL || $request->colour==NULL || $request->weight==NULL || $request->birth_date ==NULL)
        {
            return response()->json([
                "message" => 'Rellena todos los campos'
            ], 401);
        } else {
            $pet->name = $request->name;
            $pet->species = $request->species;
            $pet->breed = $request->breed;
            $pet->color = $request->colour;
            $pet->weight = $request->weight;
            $pet->birth_date = $request->birth_date;
            //$pet->photo = $request->photo;
            //$pet->documents = $request->documents;
            $pet->save();
            return response()->json([
                "message" => 'Datos de la mascota actualizados'
            ],200);
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

        $pet = Pet::where('user_id', '=', $user->id)->where('id', '=', $id)->first();

        if($pet!= NULL)
        {
            $pet->delete();

            return response()->json([
                "message" => 'Mascota eliminada correctamente'
            ], 200);

        }else{

            return response()->json([
                "message" => 'Solo puedes eliminar tus mascotas'
            ], 401);

        }
    }
}
