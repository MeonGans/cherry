<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FortuneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $arrAllProduct = [];
        $products = Product::where('quantity', '>', 0)->get();

        foreach ($products as $product) {
            for ($i = 0; $i < $product->quantity; $i++) {
                $arrAllProduct[] = ['id' => $product->id, 'name' => $product->name];
            }
        }

        $fortune = collect($arrAllProduct)->shuffle();
        $winFortune = $fortune->first();
        $jsonFortune = $fortune->toJson();

        return view('fortune', compact(['winFortune', 'jsonFortune']));
    }

    public function catch(Request $request)
    {
        $id_product = $request->input('id_product');
        $product = Product::find($id_product);

        if ($product && $product->quantity > 0) {
            $product->quantity--;
            $product->save();
        }

        return redirect()->route('fortune');
    }
}
