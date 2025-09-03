<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function registrar(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'email_verified_at' => 'nullable|date',
            'roles'=>'required|string|in:admin,user' // rol puede ser admin o user
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // hash hace que la contraseña no se vea en texto plano
            'roles'=> $request->roles,
        ]);

        try{
            $token = JWTAuth::fromUser($user);  
            return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,      
            ], 201);   
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear el Token JWT',
                'error' => $e->getMessage(),
            ], 500);  
           
        }  
    } 

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $credentials = $request->only('email', 'password');
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json([
               'success' => false,
                'message' => 'Credenciales invalidas',
            ], 401);
        }  
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);     
   }

    public function logout(){
        try{
            $user = JWTAuth::user(); // validar el usuario logeado
            JWTAuth::invalidate(JWTAuth::getToken()); // invalidar el token
            return response()->json([
                'success' => true,
                'message' => $user->name.' ha cerrado sesion correctamente',
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => ' Error al  cerrar la sesion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me ()
    {
        return response()->json([
            'success' => true,
            'user' => JWTAuth::user(),
        ], 200);
    }
    


}

