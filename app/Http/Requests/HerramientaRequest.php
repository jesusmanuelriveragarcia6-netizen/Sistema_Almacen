<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class HerramientaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(Auth::user()->rol, ['Administrador', 'Almacenero']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'almacen_id' => 'required|exists:almacenes,id',
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'required|in:Disponible,Mantenimiento,Perdido,Dañado,Falla técnica',
            'ubicacion' => 'required|string|max:100',
            'tamano' => 'nullable|string|max:50',
            'uso' => 'nullable|string|max:200',
            'stock_total' => 'required|integer|min:1|max:9999',
            'stock_minimo' => 'required|integer|min:0',
            'metadata' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'estado.in' => 'El estado seleccionado no es válido.',
            'stock_total.min' => 'El stock inicial debe ser al menos 1.',
        ];
    }
}
