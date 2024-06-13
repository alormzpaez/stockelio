<?php

namespace App\Services;

use App\Models\File;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * Save file in storage and database (associating file to some product).
     */
    public function save(int $productId, UploadedFile $file): File 
    {
        $product = Product::find($productId);

        $filename = Storage::disk('products')->put($productId, $file); // Ex: '1/abcdef.pdf'

        return $product->files()->create([
            'filename' => $filename,
            'mime_type' => $file->getMimeType(),
            'size' => intval($file->getSize()),
        ]);
    }
}
