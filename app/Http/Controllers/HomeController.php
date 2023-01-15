<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $products = Product::homePage()->get();
        $brands = Brand::homePage()->get();
        $categories = Category::homePage()->get();

        return view('index', compact('products', 'brands', 'categories'));
    }
}
