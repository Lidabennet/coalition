<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $file;

    public function __construct()
    {
        $this->file = storage_path('app/products.json');

        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    }

    private function readData()
    {
        return json_decode(file_get_contents($this->file), true);
    }

    private function saveData($data)
    {
        file_put_contents(
            $this->file,
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }

    public function index()
    {
        return view('products');
    }

    public function getProducts()
    {
        $data = $this->readData();

        usort($data, function ($a, $b) {
            return strtotime($b['datetime']) - strtotime($a['datetime']);
        });

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = $this->readData();

        $data[] = [
            'id' => uniqid(),
            'name' => $request->name,
            'quantity' => (int) $request->quantity,
            'price' => (float) $request->price,
            'total' => $request->quantity * $request->price,
            'datetime' => date('Y-m-d H:i:s')
        ];

        $this->saveData($data);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $data = $this->readData();

        foreach ($data as &$item) {
            if ($item['id'] === $id) {
                $item['name'] = $request->name;
                $item['quantity'] = (int) $request->quantity;
                $item['price'] = (float) $request->price;
                $item['total'] = $item['quantity'] * $item['price'];
            }
        }

        $this->saveData($data);

        return response()->json(['success' => true]);
    }
}
