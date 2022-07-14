<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registro(Request $request){

        // Validation form
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required|confirmed'
        ]);

        // Save User
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Return
        return response()->json([
            "mensaje" => "usuario registrado correctamente"
        ]);
    }

    public function login(Request $request){

        // Validando Login de Usuario
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        // Query User de la BD
        $user = User::where("email", "=", $request->email)->first();

        // Existe el Usurio en la BD local
        if(isset($user)){

            // El password ingresado es igual que la BD
            if(Hash::check($request->password, $user->password)){

                // Crea un token en la bd temporal
                $token = $user->createToken("auth_token")->plainTextToken;

                // Retornamos un Json con el token
                return response()->json([
                    "mensaje" => "Se inicio session",
                    "acess_token" => $token
                ]);

            } else {

                // Si el password ingresado no es igual a la BD
                return response()->json([
                    "mensaje" => "Password Incorrecto",
                    "error" => true
                ],200);

            }

        } else {

            // Si el usuario no coincide con la BD
            return response()->json([
                "mensaje" => "El usuario no conincide con la BD",
                "error" => true
            ], 200);

        }

        return "Logeando...";

    }

    public function perfil(){
        return Auth::user();
    }

    public function logout(){
        Auth::user()->tokens()->delete();
        return response()->json([
            "status" => 1,
            "mensaje" => "se cerro session correctamente.!",           
        ]);
    }

    public function test(){
        return "test";
    }
}
