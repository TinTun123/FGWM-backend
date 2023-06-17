<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatementRequest;
use App\Models\Statement;
use Illuminate\Http\Request;

 

class StatementController extends Controller
{
    //
    public function upload(StatementRequest $request) {
        
        try {

            $date = $request->input('date');
            $imageFile = $request->file('image');
    
            $filename = uniqid() . '.' .$imageFile->getClientOriginalExtension();
            $path = public_path('images');
            $imageFile->move($path, $filename);
    
            $statement = Statement::create([
                'date' => $date,
                'img_url' => asset('images/' . $filename)
            ]);
    
            return response('OK', 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong'
            ]);
        }

    }

    public function getStatement(Request $request, $page = 4) {
        
        $statements = Statement::paginate($page);
        return response()->json($statements);
    }
}
