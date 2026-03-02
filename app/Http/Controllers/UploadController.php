<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
        ]);

        try {
            $file     = $request->file('image');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store in public/uploads folder
            $path = $file->storeAs('uploads', $filename, 'public');

            // Return the public URL
            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url'     => $url,
                'path'    => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function deleteImage($key)
    {
        $allowKeys = [
            'favicon',
            'logo',
            'image_seo',
            'homepage_img',
        ];
        if (! in_array($key, $allowKeys)) {
            abort(404);
        }
        $domain = preg_replace('/^www\./', '', request()->getHost());
        $option = Setting::where('key', $key)->first();
        if (! $option) {
            return back()->withErrors('Image not found');
        }

        $option->update([
            'value' => null,
        ]);
        return back()->with('success', 'Image deleted successfully');
    }
}
