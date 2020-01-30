<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Helpers\Token;
use App\Helpers\PasswordGenerator;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{

    public function login(Request $request)
    {
        $data = ['email' => $request->email];
        $user = User::where($data)->first();
        if($user==NULL){
            return response()->json([
                "message" => 'Email o contraseña incorrecta'
            ],401);
        }
        if(decrypt($user->password) == $request->password)
        {
            $token = new token($data);
            $token = $token->encode();
            return response()->json([
                "token" => $token
            ],200);
        }
        return response()->json([
            "message" => 'Email o contraseña incorrecta'
        ],401);
    }

    public function adminLogin(Request $request)
    {
        $data = ['email' => $request->email];
        $user = User::where($data)->first();
        if($user==NULL){
            return response()->json([
                "message" => 'Email o contraseña incorrecta'
            ],401);
        }

        if(($user->role_id)!=1){
            return response()->json([
                "message" => 'Acceso denegado'
            ],401);
        }

        if(decrypt($user->password) == $request->password)
        {
            $token = new token($data);
            $token = $token->encode();
            return response()->json([
                "token" => $token
            ],200);
        }
        return response()->json([
            "message" => 'Email o contraseña incorrecta'
        ],401);
    }

    public function passrestore(Request $request)
    {
        $requested_email = ['email' => $request->email];
        $user = User::where($requested_email)->first();
        if($user==NULL){
            return response()->json([
                "message" => 'Ese email no existe'
            ],401);
        }

        $newPass = new PasswordGenerator();
        $newPass = $newPass->newPass();

        $user->password = encrypt($newPass);
        $user->save();

        $data = array("newPass" => $newPass);
        $subject = "Tu nueva contraseña";
        $for = $request->email;
        Mail::send('emails.forgot', $data, function($msj) use($subject,$for){
            $msj->from("MyPetsPassRecovery@outlook.com","MyPets Password Recovery");
            $msj->subject($subject);
            $msj->to($for);
        });

        return response()->json([
            "message" => 'Contraseña cambiada y enviada.'
        ],200);
    }

    public function showFullname(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();

        return response()->json([
            $user->fullName
        ],401);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        $usersData = array();
        $usersData = $users->toArray();

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
        $requested_email = ['email' => $request->email];

        $email = User::where($requested_email)->first();

        if($email!=NULL){
            return response()->json([
                "message" => 'Ese email ya existe'
            ],401);
        }

        $user = new User();
        $user = $user->register($request);

        $token = new token(['email' => $user]);
        $token = $token->encode();
        return response()->json([
            "token" => $token
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return response()->json([
            "message" => 'Usuario eliminado por completo correctamente'
        ],200);
    }

    public function ban($id)
    {
        $user = User::find($id);

        switch ($user->isBanned) {
            case 0:
                $user->isBanned = true;
                $user->save();
                return response()->json([
                    "message" => 'Usuario baneado correctamente'
                ],200);
            break;
            case 1:
                $user->isBanned = false;
                $user->save();
                return response()->json([
                    "message" => 'Usuario desbaneado correctamente'
                ],200);
            break;
            default:
                break;
        }
    }

    public function role($id)
    {
        $user = User::find($id);

        switch ($user->role_id) {
            case 1:
                $user->role_id = 2;
                $user->save();
                return response()->json([
                    "message" => 'Usuario degradado correctamente'
                ],200);
            break;
            case 2:
                $user->role_id = 1;
                $user->save();
                return response()->json([
                    "message" => 'Usuario ascendido correctamente'
                ],200);
            break;
            default:
                break;
        }
    }
}
