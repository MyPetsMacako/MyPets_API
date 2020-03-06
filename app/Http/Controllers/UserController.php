<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Helpers\Token;
use App\Helpers\PasswordGenerator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Contracts\Encryption\DecryptException;
use stdClass;


class UserController extends Controller
{

    public function adminRequestedUserInfo($id)
    {
        $user = User::select("id", "fullName", "nickname", "email")->where("id", "=", $id)->first();

        return response()->json(
            $user
        ,200);
    }

    public function adminPanelInfo(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $userName = User::select('fullName')->where($data)->first();

        $users = User::count();
        $pets = DB::table('pets')->count();
        $qrs = DB::table('qr')->count();
        $photos = DB::table('photos')->count();
        $appointments = DB::table('appointments')->count();
        //$reports = DB::table('reports')->count();

        $adminData = $data;
        array_push($adminData, $adminData);

        return response()->json([
            "userName" => $userName["fullName"],
            "users" => strval($users),
            "pets" => strval($pets),
            "qrs" => strval($qrs),
            "photos" => strval($photos),
            "appointments" => strval($appointments),
            "reports" => "0"
        ],200);
    }

    public function login(Request $request)
    {
        $data = ['email' => $request->email];
        $user = User::where($data)->first();
        if($user==NULL){
            return response()->json([
                "message" => 'Email o contraseña incorrecta'
            ],401);
        }
        if($user->isBanned == 1){
            return response()->json([
                "message" => 'Tu cuenta está baneada'
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

    public function showUserData(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();
        $path = 'http://www.mypetsapp.es/storage/';
        $tel_number = "(No añadido)";
        if ($user->photo != null) {
            $photo = $path . $user->photo;
        }else{
            $photo = ".";
        }
        if ($user->tel_number != null){
            $tel_number = $user->tel_number;
        }

        return response()->json([
            "name" => $user->fullName,
            "nickname" => $user->nickname,
            "email" => $user->email,
            "telephone" => $tel_number,
            "photo" => $photo
        ],200);
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
        $requested_nickname = ['nickname' => $request->nickname];

        $email = User::where($requested_email)->first();
        $nickname = User::where($requested_nickname)->first();

        if($email!=NULL){
            return response()->json([
                "message" => 'Ese email ya existe'
            ],453);
        }

        if($nickname!=NULL){
            return response()->json([
                "message" => 'Ese nickname ya existe'
            ],454);
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
    public function show()
    {

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
    public function update(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();
        $photo = Storage::putFileAs('Users', new File($request->image), "$user->id.jpg");
        $user->fullName = $request->fullName;
        $user->nickname = $request->nickname;
        $user->email = $request->email;
        $user->photo = $photo;
        $user->tel_number = $request->tel_number;
        $user->save();

        return response()->json([
            "message" => 'Datos de usuario actualizados'
        ], 200);
    }

    public function updateForAdmin(Request $request, $id)
    {
        $user = User::where('id', '=', $id)->first();

        if($request->fullName==NULL || $request->nickname==NULL || $request->email==NULL){
            return response()->json([
                "message" => 'Debes rellenar todos los campos'
            ],401);
        } else {
            $user->fullName = $request->fullName;
            $user->nickname = $request->nickname;
            $user->email = $request->email;
            $user->save();
            return response()->json([
                "message" => 'Campos actualizados'
            ],200);
        }

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

    public function restorePassword(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = new token();
        $decoded_token = $token->decode($authorization);
        $email = $decoded_token->email;
        $data = ['email' => $email];
        $user = User::where($data)->first();
        $current_password = decrypt($user->password);

        if($current_password == $request->new_password)
        {
            return response()->json([
                "message" => "La contraseña tiene que ser distinta que la anterior",
            ], 401);
        }

        if($current_password == $request->old_password) {
            if($request->new_password == $request->repeat_new_password)
            {
                $user->password = encrypt($request->new_password);
                $user->save();
                return response()->json([
                    "new password" => $request->new_password,
                ], 200);
            }
            else
            {
                return response()->json([
                    "message" => "No tienes permisos",
                ], 401);
            }
        }else {
            return response()->json([
                "message" => "Contraseña erronea",
            ], 401);
        }




    }
}
