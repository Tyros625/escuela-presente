<?php

namespace App\Http\Controllers\API;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Tenants\AccountConfiguration;
use App\Models\Tenants\File;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf,txt,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $storageType = AccountConfiguration::where('user_id', 1)->first();

        $file = $request->file('file');

        switch ($storageType->files_location) {
            case 'local':
                return $this->saveInLocal($file);
                break;
            case 'cloudinary':
                return $this->saveInCloudinary($file);
                break;
            case 's3':
                return $this->saveInS3($file);
                break;
            case 'digitalocean':
                return $this->saveInDigitalOcean($file);
                break;
        }
    }

    private function saveInS3($file)
    {
        $name = $file->getClientOriginalName();
        $filePath = 'images/'.$name;
        $uploadedFileUrl = Storage::disk('s3')->put($filePath, file_get_contents($file));

        return response()->json([
            'success' => true,
            'message' => 'File successfully uploaded',
            'file' => $uploadedFileUrl,
        ]);
    }

    private function saveInDigitalOcean($file)
    {
        $url = Storage::disk('digitalocean')->putFile('uploads', $file, 'public');

        return response()->json([
            'success' => true,
            'message' => 'File successfully uploaded',
            'file' => env('DIGITALOCEAN_SPACES_URL').$url,
        ]);
    }

    private function saveInCloudinary($file)
    {
        $uploadedFileUrl = Cloudinary::uploadFile($file->getRealPath())->getSecurePath();

        $upload = new File;
        $upload->name = $file->getClientOriginalName();
        $upload->path = $uploadedFileUrl;
        $upload->user_id = Auth::id();
        $upload->save();

        return response()->json([
            'success' => true,
            'message' => 'File successfully uploaded',
            'file' => $uploadedFileUrl,
        ]);
    }

    private function saveInLocal($file)
    {
        $path = Helper::saveFileInLocal($file, 'photos');

        $upload = new File;
        $upload->name = $file->getClientOriginalName();
        $upload->path = $path;
        $upload->user_id = Auth::id();
        $upload->save();

        return response()->json([
            'success' => true,
            'message' => 'File successfully uploaded',
            'file' => env('APP_URL').$upload->path,
        ]);
    }

    public function uploadFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf,txt,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $file = $request->file('file');

        $url = Storage::disk('digitalocean')->putFile('uploads', $file, 'public');

        return new JsonResponse(env('DIGITALOCEAN_SPACES_URL').$url);
    }
}
