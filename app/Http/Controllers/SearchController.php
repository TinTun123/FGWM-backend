<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Campagin;
use App\Models\News;
use App\Models\Protests;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    //
    public function search(Request $request) {
        $query = $request->input('query');
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $type = $request->input('type');


        if ($type === 'protest') {
            $queryBuilder = Protests::query()->with('user');
            if($query) {

                $queryBuilder->where('title', 'like', '%' . $query . '%');
            }


            if($fromDate && $toDate) {
                $queryBuilder->whereBetween('date', [$fromDate, $toDate]);
            }

            $result = $queryBuilder->get()->toArray();

            foreach ($result as &$article) {
                
                $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                $article['isVideo'] = $temp[1];
                $article['isProtrait'] = $temp[0];
                $article['thumbnail'] = $temp[2];

            }

            return response()->json($result, 200);
        }

        if ($type === 'activities') {
            $queryBuilder = Activity::query()->with('user');
            if($query) {
                $queryBuilder->where('title', 'like', '%' . $query . '%');
            }

            if($fromDate && $toDate) {
                $queryBuilder->whereBetween('date', [$fromDate, $toDate]);
            }

            $result = $queryBuilder->get()->toArray();

            foreach ($result as &$article) {
                
                $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                $article['isVideo'] = $temp[1];
                $article['isProtrait'] = $temp[0];
                $article['thumbnail'] = $temp[2];

            }

            return response()->json($result, 200);
        }

        if ($type === 'campagins') {
            $queryBuilder = Campagin::query()->with('user');

            if($query) {
                $queryBuilder->where('title', 'like', '%' . $query . '%');
            }

            if($fromDate && $toDate) {
                $queryBuilder->whereBetween('date', [$fromDate, $toDate]);
            }

            $result = $queryBuilder->get()->toArray();

            foreach ($result as &$article) {
                
                $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                $article['isVideo'] = $temp[1];
                $article['isProtrait'] = $temp[0];
                $article['thumbnail'] = $temp[2];

            }

            return response()->json($result, 200);
        }

        if ($type === 'articles') {
            $queryBuilder = Article::query()->with('user');

            if($query) {
                $queryBuilder->where('title', 'like', '%' . $query . '%');
            }

            if($fromDate && $toDate) {
                $queryBuilder->whereBetween('date', [$fromDate, $toDate]);
            }

            $result = $queryBuilder->get()->toArray();

            foreach ($result as &$article) {
                
                $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                $article['isVideo'] = $temp[1];
                $article['isProtrait'] = $temp[0];
                $article['thumbnail'] = $temp[2];

            }

            return response()->json($result, 200);
        }

        if ($type === 'news') {
            $queryBuilder = News::query()->with('user');

            if($query) {
                $queryBuilder->where('title', 'like', '%' . $query . '%');
            }

            if($fromDate && $toDate) {
                $queryBuilder->whereBetween('date', [$fromDate, $toDate]);
            }

            $result = $queryBuilder->get()->toArray();

            foreach ($result as &$article) {
                
                $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                $article['isVideo'] = $temp[1];
                $article['isProtrait'] = $temp[0];
                $article['thumbnail'] = $temp[2];

            }

            return response()->json($result, 200);
        }



    }

    private function isVideo($type, $id, $fileName) {
        try {
            //code...
            $folderPath = public_path('images' . '/' . $type . '/' . $id . '/');

            $mimeType = mime_content_type($folderPath . $fileName);
            
            if (strpos($mimeType, 'image/') === 0) {
                // image do something
                $imageSize = getimagesize($folderPath . $fileName);
    
                if ($imageSize !== false && $imageSize[0] < $imageSize[1]) {
                    $isProtrait = true;
                } else {
                    $isProtrait = false;
                }
                $isVideo = false;
                $thumbnail = '';
            } elseif (strpos($mimeType, 'video/') === 0) {
                // video do something
                $isVideo = true;
                $isProtrait = false;
                $files = scandir($folderPath . 'videoThumb');
    
                foreach($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $thumbnail = asset('images' . '/' . $type . '/' . $id . '/' . 'videoThumb' . '/' . $file);
                    }
                }
                
            }
    
            return [$isProtrait, $isVideo, $thumbnail];
        } catch (Exception $exe) {
            //throw $th;
            Log::info('Error:'[
                $exe
            ]);
        }


    }
}
