<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class OrderController extends Controller
{
    public const UNITS = [
        'piece' => 'шт.',
        'kg' => 'кг.',
        'litres' => 'л.',
    ];

    public function handle(OrderRequest $request)
    {
        $data_in = $request->validated();

        $image = $data_in['logo'];

        $data = [];

        if ($image) {
            $imagePath = $this->saveFile($image, 'images/');
            $data['logo'] = $this->convertImageToBase64($imagePath);
        }

        $data['supplier_title'] = $data_in['supplier_title'] ?? '';
        $data['order_number'] = $data_in['order_number'] ?? '';
        $data['order_date'] = date('Y-m-d');
        $data['supplier_inn'] = $data_in['supplier_inn'] ?? '';
        $data['supplier_kpp'] = $data_in['supplier_kpp'] ?? '';
        $data['supplier_address'] = $data_in['supplier_address'] ?? '';
        $data['client_fio'] = $data_in['client_fio'] ?? '';
        $data['client_inn'] = $data_in['client_inn'] ?? '';
        $data['client_address'] = $data_in['client_address'] ?? '';

        foreach ($data_in['products'] as $key => $product) {
            $data['products'][] = [
                'item' => $key + 1,
                'product_name' => $product['name'],
                'quantity' => $product['quantity'],
                'unit' => self::UNITS[$product['unit']] ?? '',
                'price' => $product['price'],
                'sum' => round($product['sum'], 2),
            ];
        }
        $data['total_quantity'] = round($data_in['total_quantity'], 2) ?? '';
        $data['total_sum'] = round($data_in['total_sum'], 2) ?? '';

        $dompdf = new Dompdf();

        $html = View::make('order', $data)->render();

        $dompdf->loadHtml($html);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf->setOptions($options);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $pdfname = 'order_' . $data['order_number'] . '.pdf';

        Storage::disk('public')->put('pdfs/' . $pdfname, $dompdf->output());

        $url = asset(Storage::url('pdfs/' . $pdfname));

        if (Storage::exists('public/' . $imagePath)) {
            Storage::delete('public/' . $imagePath);
        }

        return $url;
    }

    private function saveFile($file, $path, $name = null)
    {
        if (Storage::exists($path)) {
            Storage::makeDirectory($path, 0o755, true);
        }

        if (empty($name)) {
            if (!Storage::putFileAs('public/' . $path, $file, $file->getClientOriginalName())) {
                throw new \Exception("Unable to save file \"{$file->getClientOriginalName()}\"");
            }
            return $path . $file->getClientOriginalName();
        }

        if (!Storage::putFileAs('public/' . $path, $file, $name)) {
            throw new \Exception("Unable to save file \"$name\"");
        }
        return $path . $name;
    }

    public function convertImageToBase64($path)
    {
        $imageData = Storage::disk('public')->get($path);

        $type = pathinfo(Storage::url($path), \PATHINFO_EXTENSION);

        $base64Image = base64_encode($imageData);

        return '<img src="data:image/' . $type . ';base64,' . $base64Image . '" alt="logo" style="width: 200px; height: auto;">';
    }
}
