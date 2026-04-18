<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->addresses);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string',
            'phone' => 'required|string',
            'full_address' => 'required|string',
            'instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $address = $request->user()->addresses()->create($request->all());

        return response()->json([
            'message' => 'Adresse ajoutée avec succès',
            'address' => $address
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->find($id);

        if (!$address) {
            return response()->json(['message' => 'Adresse non trouvée'], 404);
        }

        $address->delete();

        return response()->json(['message' => 'Adresse supprimée']);
    }
}
