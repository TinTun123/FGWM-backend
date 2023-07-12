<?php

namespace App\Http\Controllers;

use App\Models\Medias;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    //
    public function create(Request $request) {
        $request->validate([
            'mediaURL' => 'required|string'
        ]);

        $media = new Medias;
        $media->mediaURL = $request->input('mediaURL');
        $media->save();

        return response()->json([
            $media
        ], 200);
    }

    public function getMedia(Request $request) {
        $medias = Medias::get()->toArray();
        return response()->json($medias, 200);
    }

    public function deleteMedia(Request $request, $id) {
        
        $media = Medias::findOrFail($id);
        $media->delete();
        return response()->json(['successful'], 200);
    }
}
