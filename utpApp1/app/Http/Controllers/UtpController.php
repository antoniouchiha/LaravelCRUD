<?php

namespace App\Http\Controllers;

use App\Models\Utp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UtpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los datos y devolverlos como JSON
        return response()->json(Utp::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
            'img' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Guardar la imagen en el disco público
        $path = $request->file('img')->store('images', 'public');

        // Crear un nuevo registro en la base de datos
        $dato = Utp::create([
            'descripcion' => $validated['descripcion'],
            'img' => $path,
        ]);

        // Responder con el dato recién creado
        return response()->json($dato, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Intentar encontrar el dato por su ID
        try {
            $dato = Utp::findOrFail($id);
            return response()->json($dato, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Dato no encontrado'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Buscar el dato por ID
            $dato = Utp::findOrFail($id);
    
            // Validar los datos enviados
            $validated = $request->validate([
                'descripcion' => 'nullable|string|max:255',
                'img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
    
            // Actualizar la imagen si está presente en la solicitud
            if ($request->hasFile('img')) {
                // Eliminar la imagen anterior si existe
                if ($dato->img) {
                    Storage::disk('public')->delete($dato->img);
                }
    
                // Guardar la nueva imagen
                $path = $request->file('img')->store('images', 'public');
                $dato->img = $path;
            }
    
            // Actualizar la descripción si está presente en la solicitud
            if ($request->filled('descripcion')) {
                $dato->descripcion = $request->input('descripcion');
            }
    
            // Guardar solo si hay cambios
            if ($dato->isDirty()) {
                $dato->save();
            }
    
            // Responder con el dato actualizado
            return response()->json(['message' => 'Dato actualizado', 'data' => $dato], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Dato no encontrado'], 404);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Buscar el dato por ID
            $dato = Utp::findOrFail($id);

            // Eliminar la imagen asociada si existe
            if ($dato->img) {
                Storage::disk('public')->delete($dato->img);
            }

            // Eliminar el dato de la base de datos
            $dato->delete();

            // Responder con el mensaje de éxito
            return response()->json(['message' => 'Dato eliminado'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Dato no encontrado'], 404);
        }
    }
}
