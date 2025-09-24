<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validator = \Validator::make($request->all(), [
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ], [
            'images.required' => 'Vui lòng chọn file để tải lên',
            'images.*.image'  => 'File tải lên phải là ảnh',
            'images.array'    => 'Dữ liệu tải lên không hợp lệ',
            'images.*.mimes'  => 'File tải lên phải có định dạng: jpg, jpeg, png, gif',
            'images.*.max'    => 'Kích thước file tải lên không được vượt quá 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        $uploadedFiles = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // Lưu file vào storage/app/public/products
                $path            = $file->store('products', 'public');
                $uploadedFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'url'  => Storage::url($path),
                    'size' => $file->getSize(),
                ];
            }
        }

        return response()->json([
            'files' => $uploadedFiles,
        ], 200);
    }
    public function destroy(int $id)
    {
        $image = ProductImage::findOrFail($id);

        if ($image) {
            $filePath = 'products/' . $image->filename;

            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                $image->delete();

                return Inertia::location(url()->previous());
            }
        }

        return Redirect::back()->with('error', 'File không tồn tại!');
    }
}
