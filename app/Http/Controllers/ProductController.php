<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private $product;

    public function __construct(ProductRepositoryInterface $product){
        $this->product = $product;
    }

    public function getAll(){
        $user = auth()->user();
        if (!$user){
            return response()->json([
                'message' => 'usuario no autenticado',
            ], 401);
        }
        $products = $this->product->getAll($user->id);
        return response()->json([ "data" => (count($products) > 0) ? $products : 'El usuario no tiene productos asociados'
        ]);

    }
    public function create(Request $request){
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|int|min:1',
            'amount' => 'nullable|int|min:1',
        ]);

        if ($validate->fails()){
            return response()->json([
                'message' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $fields = $request->all();
        $user = auth()->user();
        if (!$user){
            return response()->json([
                'message' => 'usuario no autenticado',
            ], 401);
        }
        $fields['user_id'] = $user->id;

        $product = $this->product->create($fields);

        return response()->json([
            'message' => ($product) ? "Producto creado" : "No se pudo almacenar el producto"
        ]);
    }
    public function update(Request $request){
        $validate = Validator::make($request->all(), [
            'id' => 'required|int|min:1|exists:products,id',
            'name' => 'required|string|max:255',
            'price' => 'required|int|min:1',
            'amount' => 'nullable|int|min:1',
        ]);

        if ($validate->fails()){
            return response()->json([
                'message' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $fields = $request->all();
        $user = auth()->user();
        if (!$user){
            return response()->json([
                'message' => 'usuario no autenticado',
            ], 401);
        }
        $fields['user_id'] = $user->id;
        $id = $request['id'];
        unset($fields['id']);

        $product = $this->product->update($id,$fields);

        return response()->json([
            'message' => ($product) ? "Producto actualizado" : "No se pudo actualizar el producto"
        ]);
    }
    public function delete(Request $request){
        $validate = Validator::make($request->all(), [
            'id' => 'required|int|min:1|exists:products,id',
        ]);

        if ($validate->fails()){
            return response()->json([
                'message' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }

        $user = auth()->user();
        if (!$user){
            return response()->json([
                'message' => 'usuario no autenticado',
            ], 401);
        }

        $product = $this->product->delete($request['id']);

        return response()->json([
            'message' => ($product) ? "Producto borrado" : "No se pudo borrar el producto"
        ]);

    }
}
