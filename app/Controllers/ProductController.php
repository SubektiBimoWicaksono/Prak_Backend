<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\ProductModel;
use CodeIgniter\API\ResponseTrait;

class ProductController extends BaseController {
    use ResponseTrait;

    public function __construct(){
        $this->product = new ProductModel();
    }
    public function insertProduct(){
        $data = [
            'nama_product' => $this->request->getPost('nama_product'),
            'description' => $this->request->getPost('description')
        ];

        $this->product->insertProductORM($data);
        return redirect()->to('products');
    }

    public function insertProductApi(){
        $requestData = $this->request->getJSON();
        $validation = $this->validate([
            'nama_product' => 'required',
            'description' => 'required',
        ]);
    
        if(!$validation) {
            $this->response->setStatusCode(400);
            return $this->response->setJSON([
                'code'=> 400,
                'status'=>'BAD REQUEST',
                'data'=>'produk belum ditambahkan'
            ]);
        }
    
        $data = [
            'nama_product' => $requestData->nama_product, // Correct property name
            'description' => $requestData->description,
        ];
    
        $insert = $this->product->insertProductORM($data);
        if ($insert) {
            return $this->respond([
                'code' => 200,
                'status' => 'OK',
                'data' => $data
            ]);
        } else {
        $this->response->setStatusCode(500);
        return $this->response->setJSON([
            'code'=> 500,
            'status'=>'INTERNAL SERVER ERROR',
            'data'=>'null'
            ]);
        }
    }
    

    public function insertPage(){
        return view('insert_product');
    }
    
    public function readProduct(){
        $products = $this->product->findAll();
        $data = [
            'data' => $products
        ];

        return view('product', $data);
    }

     public function readProductApi(){
        $products = $this->product->findAll();
        return $this->respond([
            'code'=>200,
            'status'=>'OK',
            'data'=>$products
        ]);
    }

    public function getProduct($id){
        $product = $this->product->where('id', $id)->first();
        $data = [
            'product' => $product
        ];
        return view('edit_product', $data);
    }

    public function getProductApi($id){
        $product = $this->product->where("id", $id)->first();
        if (!$product) {
            $this->response->setStatusCode(404);
            return $this->response->setJSON([
            'code'=> 404,
            'status'=>'NOT FOUND',
            'data'=>'product not found'
            ]);
        }

        return $this->respond([
            'code'=>200,
            'status'=>'OK',
            'data'=>$product
        ]);
    }

    public function updateProduct($id){
        $nama_product = $this->request->getVar('nama_product');
        $description = $this->request->getVar('description');
        $data = [
            'nama_product' => $nama_product,
            'description' => $description
        ];
        $this->product->update($id, $data);
        return redirect()->to(base_url("products"));
    }

    public function updateProductApi($id){
        $requestData = $this->request->getJSON();
        $validation = $this->validate([
            'nama_product' => 'required',
            'description' => 'required',
        ]);
    
        if (!$validation) {
            $this->response->setStatusCode(400);
            return $this->response->setJSON([
                'code' => 400,
                'status' => 'BAD REQUEST',
                'data' => 'Invalid input data'
            ]);
        }
    
        $data = [
            'nama_product' => $requestData->nama_product,
            'description' => $requestData->description,
        ];
    
        // Check if the product exists
        $existingProduct = $this->product->find($id);
    
        if (!$existingProduct) {
            $this->response->setStatusCode(404);
            return $this->response->setJSON([
                'code' => 404,
                'status' => 'NOT FOUND',
                'data' => 'Product not found'
            ]);
        }

            $update = $this->product->update($id, $data);
    
            if ($update) {
                return $this->respond([
                    'code' => 200,
                    'status' => 'OK',
                    'data' => $data
                ]);
            } else {
                $this->response->setStatusCode(500);
                return $this->response->setJSON([
                    'code'=> 500,
                    'status'=>'INTERNAL SERVER ERROR',
                    'data'=>'null'
                    ]);
                }
        
            $this->response->setStatusCode(500);
            return $this->response->setJSON([
                'code' => 500,
                'status' => 'INTERNAL SERVER ERROR',
                'data' => $e->getMessage()
            ]);
        
    }
    
    

    public function deleteProduct($id){
        $this->product->delete($id);
        return redirect()->to(base_url("products"));

    }

    public function deleteProductApi($id) {
        // Check if the product exists
        $existingProduct = $this->product->find($id);
    
        if (!$existingProduct) {
            $this->response->setStatusCode(404);
            return $this->response->setJSON([
                'code' => 404,
                'status' => 'NOT FOUND',
                'data' => 'Product not found'
            ]);
        }
    
        // Delete the product and handle errors
        try {
            $this->product->delete($id);
    
            return $this->respond([
                'code' => 200,
                'status' => 'OK',
                'data' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            $this->response->setStatusCode(500);
            return $this->response->setJSON([
                'code' => 500,
                'status' => 'INTERNAL SERVER ERROR',
                'data' => $e->getMessage()
            ]);
        }
    }
    
}