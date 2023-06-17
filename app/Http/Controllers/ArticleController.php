<?php

namespace App\Http\Controllers;

use App\Models\Protests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ArticleController extends Controller
{
    //
    public function create(Request $request) {

        try {
            $protest = new Protests;
            $user_id = Auth::id();
            $protest->id = $this->generateNewId();
            $protest->title = $request->input('title');
            $protest->date = $request->input('date');
            $protest->bodyText = $request->input('content');
            $protest->total_msg = 6;
            $protest->total_view = 12;
            $protest->user_id = $user_id;
    
            if($request->hasFile('coverImg')) {
                $protest->imgURL = $this->storeImage($request->file('coverImg'), $protest->id, $request->input('type'));
            }
    
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $this->storeThumbnails($images, $protest->id, $request->input('type'));
            }
    
            $protest->save();
    
            return response()->json([
                $protest
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred during the operation',
                500
            ]);
        }
    }

    private function storeImage($image, $protetId, $type) {
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $folderPath = 'images/' . $type . '/' . $protetId;

        if(!File::exists(public_path($folderPath))) {

            File::makeDirectory(public_path($folderPath), 0777, true, true);
        }

        $image->move(public_path($folderPath), $filename);
        $publicURL = asset($folderPath . '/' . $filename);

        return $publicURL;
    }

    private function storeThumbnails($images, $protestId, $type) {
        $folderPath = public_path('images/' . $type . '/' . $protestId . '/' . 'thumbnails/');

        if(!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true, true);
        }

        foreach ($images as $image) {
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($folderPath, $filename);
        }
    }

    private function generateNewId() {
        $lastInsertId = Protests::max('id');
        $nextId = ($lastInsertId !== null) ? ($lastInsertId + 1) : 1;

        return $nextId;
    }
}

