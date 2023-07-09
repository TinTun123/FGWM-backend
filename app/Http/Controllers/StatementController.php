<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatementRequest;
use App\Models\Statement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StatementController extends Controller
{
    //
    public function upload(StatementRequest $request) {
        
        try {

            $date = $request->input('date');
            $isjoint = $request->input('isjoint');
            $imageFile = $request->file('image');
            
            $id = $this->generateNewId();
            $statement = new Statement();
            $statement->date = $date;
            $statement->id = $id;
            $statement->img_url = $this->storeStateImages($imageFile, $id);
            
            if ($isjoint === 'true') {
                $statement->isjoint = true;
            }

            $statement->save();
            return response()->json([$statement]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong'
            ]);
        }

    }

    public function deleteStatement(Request $request, $id) {
        $statement = Statement::findOrFail($id);
        $folderPath = 'http://192.168.1.121:8000/images/statements/' . $id;

        if(File::exists($folderPath)) {
            File::deleteDirectory($folderPath);
        }

        $statement->delete();

        return response()->json([
            $id
        ], 200);
    }

    public function getStatement(Request $request, $page = 4) {
        
        $statements = Statement::where('isjoint', false)->paginate($page);
        return response()->json($statements);
    }

    public function getOneStatement(Request $request, $id) {
        $folderPath = public_path('images/statements/' . $id);

        if (!File::isDirectory($folderPath)) {
            return response()->json([
                'error' => 'Folder not found'
            ], 400);
        }

        $imagesPath = [];

        $files = File::files($folderPath);

        foreach($files as $file) {
            $filename = $file->getFilename();

            $publicPath = asset("images/statements/{$id}/{$filename}");

            $imagesPath[] = $publicPath;
        }

        return response()->json([
            'imagesPath' => $imagesPath
        ], 200);
    }

    public function getJointStatement(Request $request, $page = 4) {
        $statements = Statement::where('isjoint', true)->paginate($page);
        return response()->json($statements);
    }

    private function storeStateImages($images, $stateId) {
        $folderPath = 'images/statements/' . $stateId;

        if(!File::exists(public_path($folderPath))) {
            File::makeDirectory($folderPath, 0777, true, true);
        }

        $firstImagePath = null;

        foreach ($images as $image) {
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path($folderPath), $filename);

            if ($firstImagePath === null) {
                $firstImagePath = asset('images/statements/' . $stateId . '/' . $filename);
            }
        }

        return $firstImagePath;
    }

    private function generateNewId() {

        $lastInsertId = Statement::max('id');
        $nextId = ($lastInsertId !== null) ? ($lastInsertId + 1) : 1;

        return $nextId;
    }
}
