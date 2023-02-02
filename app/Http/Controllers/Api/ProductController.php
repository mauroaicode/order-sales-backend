<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditProductRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductsResources;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /*=============================================
    OBTENER TODOS LOS USUARIOS
    =============================================*/
    public function getProducts(): \Illuminate\Http\JsonResponse
    {
        $products = ProductsResources::collection(
            Product::latest()->get()
        );
        if (count($products) === 0) return response()->json(['No hay productos registrados.'], 401);
        return response()->json([
            'success' => true,
            'message' => 'Get Products',
            'response' => 'get_products',
            'total' => $products->count(),
            'data' => $products,
        ], 200);
    }

    /*=============================================
        AGREGAR PRODUCTO
    =============================================*/
    public function addProduct(ProductRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction(); //Inicializamos la transacción
        try {
            $product = Product::create([
                'product_name' => ucwords($request['name']),
                'product_description' => $request['description'],
                'price' => $request['price'],
                'picture' => $request['picture'],
                'product_slug' => Str::slug(strtolower($request['name']) . '-' . Str::random(10), '-')
            ]);

            DB::commit(); // Si todo sale bien realizamos el commit o transacción a la base de datos
            return response()->json([
                'success' => true,
                'message' => 'Add Product',
                'response' => 'add_product',
                'data' => $product
            ], 200);
        } catch (\Throwable $th) {
            /* Si sale un error realizamos un rollback a la base de datos, mostramos el error en el en archivo Log*/
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR ADD PRODUCT.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }

    /*=============================================
    EDITAR PRODUCTO
    =============================================*/
    public function editProduct(EditProductRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction(); //Inicializamos la transacción
        try {
            $product = Product::find($id);
            /* Validamos que existe el usuario en la base de datos*/
            if (!$product) return response()->json(['El producto que quieres editar no existe.'], 404);

            if ($request['name'] !== $product->product_name) {
                $product->product_slug = Str::slug($request['name'] . '-' . Str::random(10), '-');
            }
            $product->product_name = ucwords($request['name']);
            $product->product_description = ucwords($request['description']);
            $product->price = ucwords($request['price']);

            $product->save();

            DB::commit(); // Si todo sale bien realizamos el commit o transacción a la base de datos
            return response()->json([
                'success' => true,
                'message' => 'Edit Product',
                'response' => 'edit_product',
                'data' => $product
            ], 200);
        } catch (\Throwable $th) {
            /* Si sale un error realizamos un rollback a la base de datos, mostramos el error en el en archivo Log*/
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR EDIT PRODUCT.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }

    /*=============================================
         ELIMINAR PRODUCTO
       =============================================*/
    public function deleteProduct($id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $product = Product::where('id', $id)->first();
            /* Validamos que existe el usuario en la base de datos*/
            if (!$product) return response()->json(['El producto que quieres eliminar no existe.'], 404);

            $product->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Delete Product',
                'response' => 'delete_product',

            ], 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR DELETE PRODUCT.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }

    /*=============================================
    CARGAR IMAGEN DEL PRODUCTO PRODUCTO
  =============================================*/
    public function uploadPicture(Request $request, $id): \Illuminate\Http\JsonResponse
    {

        $product = Product::latest()->first(); //Obtenemos el último producto creado


        $fileName = Str::random(10) . '-' . str_replace(' ', '-', $product->product_name); //Creamos un nombre para el archivo
        $file = $request->file('file'); //La imagen recibida
        Storage::disk('public')->put('/products/' . $fileName . '.' . $file->getClientOriginalExtension(), file_get_contents($file)); //Guardamos la imagen en el storage
        $urlFinal = '/storage/products/' . $fileName . '.' . $file->getClientOriginalExtension(); //Obtenemos la url final, y es la que guardaremos en la base de datos

        if (!$product) return response()->json(['No se puede agregar la imagen porque el producto no existe.'], 404);

        DB::beginTransaction();
        try {
            if ($id !== 0 || $id !== '0') {
                $productEdit = Product::find($id);
                $productEdit->picture = $urlFinal;
                $productEdit->save();
            } else {
                $product->update([
                    'picture' => $urlFinal
                ]); //Guardamos la imagen al producto correspondiente
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Upload Picture',
                'response' => 'upload_picture',
                'path_file' => $urlFinal,

            ], 200);
        } catch (\Throwable $th) {
            $response = [
                'success' => false,
                'message' => 'Transaction Error',
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ];
            Log::error('LOG ERROR UPLOAD PICTURE.', $response); // Guardamos el error en el archivo de logs
            DB::rollBack();
            return response()->json($response, 500);
        }
    }
}
