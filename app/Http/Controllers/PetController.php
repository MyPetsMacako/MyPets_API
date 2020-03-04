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

    public function getQRData($id)
    {
        $pet = Pet::where('pets.id', '=', $id)
                        ->join('users', 'users.id', '=', 'pets.user_id')
                        ->select('pets.name', 'pets.photo', 'users.fullName', 'users.email', 'users.tel_Number')
                        ->get();

        $photo = $pet[0]["photo"];
        $path = "http://mypetsapp.es/storage/";
        $photo = $path . $photo;
        $pet[0]["photo"] = $photo;
        return response()->json(
            $pet
        ,200);
    }

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
                "message" => 'El id del usuario introducido no existe'
            ],401);
        }
        else
        {
            $qrContent = $this->QRContent($pet);
            return response()->json([
                "qrContent" => $qrContent
            ],200);
        }
    }

    public function QRContent($pet): String
    {
        $url = "http://mypetsapp.es/adminpanel-MyPets/scannedQR.html?id=";
        $petid = $pet;
        $qrContent = strval($url . $petid);
        return $qrContent;
    }

    public function saveQR(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();

        $url = $request->url;
        $result = explode("/",$url);

        if ($request->qr != NULL)
        {
            $photo = Storage::putFileAs('QR', new File($request->qr), "qr_"+"$user->id$pet->name.jpg");
            $pet->qr = $photo;
        } else {
            print("error");
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
        $path = 'http://mypetsapp.es/storage/';
        $pets = Pet::where('user_id', $user->id)->get();
        $ids = array();
        $names = array();
        $breeds = array();
        $weights = array();
        $colors = array();
        $birth_dates = array();
        $images = array();
        $documents= array();
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
                $file = "$path" .   $pet->document;
                array_push($documents, $file);
            }
        }

        return response()->json(
            ["ids"=>$ids, "names"=>$names, "breeds"=>$breeds,"weights"=>$weights,"colors"=>$colors,"birth_dates"=>$birth_dates, "images" => $images, "documents" => $documents]
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

    public function adminDestroy($id)
    {
        $pet = Pet::where('id', '=', $id)->first();

        if($pet!= NULL)
        {
            $pet->delete();

            return response()->json([
                "message" => 'Mascota eliminada correctamente'
            ], 200);

        }else{

            return response()->json([
                "message" => 'Esa mascota no existe'
            ], 401);

        }
    }
}
