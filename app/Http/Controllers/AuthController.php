<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // Importar Mail
use App\Mail\SendUserPassword; // Importar el Mailable para el correo


class AuthController extends Controller
{
    public function checkSession(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            return response()->json([
                'status' => 'success',
                'message' => 'Token is valid.',
                'user' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated.'
            ], 401);
        }
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // 'type' => 1,
            'password' => Hash::make($request->password),
        ]);

        // Enviar correo con la contraseña
        try {
            Mail::to($user->email)->send(new SendUserPassword($request->password));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Usuario registrado, pero el correo no pudo enviarse', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Usuario registrado con éxito y correo enviado'], 201);
    }

    public function register_width_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Usuario registrado con éxito'], 201);
    }

    public function register_width_operation_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
            'operation' => 'required|string|max:255', // Validar el número de operación
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Registrar la transacción con el número de operación
        $transaction = Transaction::create([
            'id_user' => $user->id,         // Asignar el ID del usuario recién creado
            'operation' => $request->operation, // El número de operación ingresado
        ]);

        return response()->json(['message' => 'Usuario y operación registrados con éxito'], 201);
    }

    public function login(Request $request)
    {
        // Valida los campos 'phone' y 'password'
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Si la validación falla, retorna un error
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Intenta autenticar usando 'phone' y 'password'
        if (!auth()->attempt(['phone' => $request->phone, 'password' => $request->password])) {
            return response()->json([
                'message' => 'Datos inválidos'
            ], 401);  // Código de estado 401 para credenciales inválidas
            
        }

        // Si la autenticación es exitosa, genera el token
        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        // Retorna el token y los detalles del usuario
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer', 'user' => auth()->user()]);
    }

}
