<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

trait SaveFile
{

    protected function saveFile($file, string $model_name)
    {
        if (App::environment(['testing', 'local', 'staging'])) {
            $model_name = "testing/{$model_name}";
        }

        if (in_array(Str::lower($file->getExtension()), ['jpg', 'png', 'jpeg', 'gif'])) {
            $model_name = "{$model_name}/";

            $resize_file = Image::make($file)->orientate();

            $upload_file_name = $this->generateFileName($file, $model_name) . '.' . $file->getExtension();

            $path = Storage::put($model_name . $upload_file_name, $resize_file->stream()->__toString());

            $path = $model_name . $upload_file_name;

            return $path;
        }

        $path = $file->store($model_name);

        return $path;
    }

    /**
     * @return string
     */
    protected function generateFileName($file, $path)
    {
        $filename = Str::random(20);

        // Make sure the filename does not exist, if it does, just regenerate
        while (Storage::exists($path . $filename . '.' . $file->getExtension())) {
            $filename = Str::random(20);
        }

        return $filename;
    }

    public function deleteDirectory($path)
    {
        // if (count(Storage::exists($path))) {
        try {
            //code...
            Storage::deleteDirectory($path);
        } catch (\Throwable $th) {
            //throw $th;
        }
        // }
    }

    public function deleteFile($path)
    {

        try {
            //code...
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
