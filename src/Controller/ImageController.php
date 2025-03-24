<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function detach_image(Request $request)
    {
        try {
            $modelType = $request->input('model_type');
            $modelId = $request->input('model_id');
            $modelInstance = App::make($modelType)->find($modelId);
            $img = Image::find($request->input('image'));
            $filePath = $img->image;
            $flag = $modelInstance->image()->where('id', $img->id)->delete();
            Storage::delete($filePath);
            return back()->withSuccess('Image was deleted!');
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Model not found.');
        }
    }
}
