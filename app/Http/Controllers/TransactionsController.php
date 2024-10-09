<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class TransactionsController extends Controller
{

    public function getUserTransactions(Request $request)
    {
        try {
            // Obtener el usuario autenticado
            $user = auth()->user();
    
            // Validar que haya un usuario autenticado
            if (!$user) {
                return response()->json(['message' => 'Usuario no autenticado'], 401);
            }

            // Obtener las transacciones del usuario paginadas
            $transactions = Transaction::where('id_user', $user->id)->paginate(10);  // Puedes ajustar la cantidad de paginación
            
            // Devolver la respuesta JSON
            return response()->json($transactions, 200);
    
        } catch (\Exception $e) {
            // Loguear el error
            Log::error('Error al obtener los registros: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los registros. '], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Obtener todos los datos del request
        $data = $request->all();

        try {
            // Validar los datos del request
            $validatedData = $request->validate([
                'operation' => 'required|integer',
            ]);

            // Log::info("Datos validados correctamente", ['validatedData' => $validatedData]);

            // Obtener el usuario autenticado
            $user = $request->user();
            // Log::info("Usuario autenticado", ['user' => $user]);

            // Crear un nuevo registro
            $operation = new Transaction();
            $operation->id_user = $user->id; // ID del usuario autenticado

            // Asignar valores validados a los campos del modelo 
            $operation->fill($validatedData);

            // Guardar en la base de datos
            $operation->save();

            // Retorna una respuesta de éxito
            return response()->json(['message' => 'Registro creado con éxito', 'operacion' => $operation], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar y devolver errores de validación
            // Log::error('Error de validación', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Error de validación', 'errors' => $e->errors(), 'request' => $data], 422);
        } catch (\Exception $e) {
            // Loguea el error
            // Log::error('Error al guardar el registro: ' . $e->getMessage(), ['request' => $data]);
            return response()->json(['error' => 'Error al guardar el registro', 'message' => $e->getMessage(), 'request' => $data], 500);
        }
    }
    

}








