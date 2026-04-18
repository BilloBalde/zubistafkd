<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProductsExport implements FromView, WithDrawings
{
    public function view(): View
    {
        $dataTable = Product::with('categories', 'stores')->get();
        $userStoreId = auth()->user()->role_id == 3
            ? Store::where('user_id', auth()->user()->id)->value('id')
            : null;

        return view('products.export', compact('dataTable', 'userStoreId'));
    }
    public function drawings()
    {
        $drawings = [];

        $products = Product::all();
        foreach ($products as $index => $product) {
            $drawing = new Drawing();
            $drawing->setName('Product Image');
            $drawing->setDescription('Product Image');
            $drawing->setPath(public_path('products/' . $product->image));
            $drawing->setHeight(100);
            $drawing->setCoordinates('G' . ($index + 2)); // Adjust for your table structure
            $drawings[] = $drawing;
        }

        return $drawings;
    }
}
