<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Campagin;
use App\Models\MigrationCom;
use App\Models\Protests;
use App\Models\User;
use App\Models\Women;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use function PHPSTORM_META\map;

class ArticleController extends Controller
{
    //
    public function create(Request $request) {

        try {

            $type = $request->input('type');
            
            if($type === 'protest') {

                $article = new Protests;

            } elseif ($type === 'activities') {

                $article = new Activity;
            } elseif ($type === 'articles') {
                $article = new Article;
            } elseif ($type === 'campagins') {
                $article = new Campagin;
            } elseif ($type === 'women') {
                $article  = new Women;
            } elseif ($type === 'migration') {
                $article = new MigrationCom;
            }

            $user_id = Auth::id();
            $article->id = $this->generateNewId($type);
            $article->title = $request->input('title');
            $article->date = $request->input('date');
            $article->bodyText = $request->input('content');
            if($type !== 'articles' && $type !== 'campagins' && $type !== 'women' && $type  !== 'migration') {
                $article->total_msg = 6;
            }
            $article->total_view = 12;
            $article->user_id = $user_id;

            if($request->hasFile('coverImg')) {

                $article->imgURL = $this->storeImage($request->file('coverImg'), $article->id, $request->input('type'));

            }
    
            if ($request->hasFile('images')) {

                $images = $request->file('images');
                $this->storeThumbnails($images, $article->id, $request->input('type'));

            }
    
            $article->save();

            $article->user = User::find($user_id);
    
            return response()->json([
                $article
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                500
            ]);
        }
    }

    public function showProtest(Request $request, $type) {
        try {

            if ($type === 'protest') {

                $protests = Protests::withCount('messages')->with('user')->get()->toArray();
                foreach ($protests as &$protest) {
                    $protest['created_at'] = Carbon::parse($protest['created_at'])->format('d M Y');
                }
                
                return response()->json($protests);

            } else if ($type === 'activities') {

                $activities = Activity::withCount('messages')->with('user')->get()->toArray();
                
                foreach ($activities as &$activity) {
                    $activity['created_at'] = Carbon::parse($activity['created_at'])->format('d M Y');
                }

                return response()->json($activities);

            } else if ($type === 'articles') {

                $articles = Article::withCount('messages')->with('user')->get()->toArray();

                foreach ($articles as &$article) {
                    $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                }
                return response()->json($articles);

            } else if ($type === 'campagins') {
                $articles = Campagin::withCount('messages')->with('user')->get()->toArray();

                foreach ($articles as &$article) {
                    $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                }

                return response()->json($articles);
            } else if ($type === 'women') {
                $articles = Women::withCount('messages')->with('user')->get()->toArray();

                foreach ($articles as &$article) {
                    $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                }

                return response()->json($articles);
            } else if ($type === 'migration') {
                $articles = MigrationCom::withCount('messages')->with('user')->get()->toArray();

                foreach ($articles as &$article) {
                    $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                }

                return response()->json($articles);
            }

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex,
                500
            ]);
        }
    }

    public function editProtest(Request $request, $type, $id) {
        try {

            if($type === 'protest') {

                $article = Protests::with('user')->find($id);

            } elseif ($type === 'activities') {

                $article = Activity::with('user')->find($id);

            } elseif($type === 'articles') {

                $article = Article::with('user')->find($id);

            } elseif ($type === 'campagins') {
                $article = Campagin::with('user')->find($id);
            }  elseif ($type === 'women') {
                $article = Women::with('user')->find($id);
            } elseif ($type === 'migration') {
                $article = MigrationCom::with('user')->find($id);
            }



            $user_id = Auth::id();

            if(!$article) {
                return response()->json(['message' => 'Article not found'], 404);
            }

            $title = $request->input('title');

            if(!$title) {
                return response()->json([
                    'message' => 'No title found',
                    400
                ]);
            }

            if($title) {
                $article->title = $title;
            }

            $date = $request->input('date');
            if($date) {
                $article->date = $date;
            }

            $bodyText = $request->input('content');
            if($bodyText) {
                $article->bodyText = $bodyText;
            }


            if($request->hasFile('coverImg')) {

                $article->imgURL = $this->storeImage($request->file('coverImg'), $id, $type);

            }
            $folderPath = public_path('images/' . $type . '/' . $id . '/' . 'thumbnails/');
            if(File::exists($folderPath)) {
                File::deleteDirectory($folderPath);
            }

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $this->storeThumbnails($images, $id, $request->input('type'));
            }

            $article->save();
            return response()->json([$article]);

        } catch (Exception $ece) {
            return response()->json([
                'error' => $ece,
                500
            ]);
        }
    }

    public function deleteProtest(Request $request, $type, $id) {
        try {

            if($type === 'protest') {

                $article = Protests::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = Protests::latest()->first();

            } elseif ($type === 'activities') {

                $article = Activity::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = Activity::latest()->first();

            } elseif ($type === 'articles') {

                $article = Article::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = Article::latest()->first();

            } elseif ($type === 'campagins') {

                $article = Campagin::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = Campagin::latest()->first();

            } elseif ($type === 'Women') {

                $article = Women::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = Women::latest()->first();

            } elseif ($type === 'migration') {

                $article = MigrationCom::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = MigrationCom::latest()->first();

            }



            $folderPath = public_path('images/' . $type . '/' . $id);
            if(File::exists($folderPath)) {
                File::deleteDirectory($folderPath);
            }

            return response()->json([
                 $lastProtest->id
            ]);
        } catch (Exception $exc) {
            return response()->json([
                'error' => 'Delete operation failed',
                500
            ]);
        }
    }


    public function showThumbnails(Request $request, $type, $id) {

        $folderPath = 'images/' . $type . '/' . $id . '/thumbnails';
        
        if(!file_exists(public_path($folderPath)) || !is_dir(public_path($folderPath))) {
            return response()->json([]);
        }

        $files = scandir(public_path($folderPath));
        $links = [];

        foreach($files as $file) {

            if($file !== '.' && $file !== '..') {
                $publicUrl = asset($folderPath . '/' . $file);
                $links[] = $publicUrl;
            }

        }
        if ($type === 'protest') {

            $article = Protests::findOrFail($id);

        } elseif ($type === 'activities') {

            $article = Activity::findOrFail($id);

        } elseif ($type === 'articles') {

            $article = Article::findOrFail($id);

        } elseif ($type === 'campagins') {

            $article = Campagin::findOrFail($id);

        } elseif ($type === 'women') {

            $article = Women::findOrFail($id);

        } elseif ($type === 'migration') {

            $article = MigrationCom::findOrFail($id);

        }

        $article->increment('total_view');


        return response()->json($links);

    }

    private function storeImage($image, $protetId, $type) {
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $folderPath = 'images/' . $type . '/' . $protetId;

        if(is_dir($folderPath)) {

            $files = scandir($folderPath);


            foreach($files as $file) {
                if(is_file(public_path($folderPath) . '/' . $file)) {
                    unlink(public_path($folderPath) . '/' . $file);
                }

            }
        }

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
 
    

    private function generateNewId($type) {

        if($type === 'protest') {
            $lastInsertId = Protests::max('id');
        } elseif ($type === 'activities') {
            $lastInsertId = Activity::max('id');
        } elseif ($type === 'articles') {
            $lastInsertId = Article::max('id');
        } elseif ($type === 'campagins') {
            $lastInsertId = Campagin::max('id');
        } elseif ($type === 'women') {
            $lastInsertId = Women::max('id');
        } elseif ($type === 'migration') {
            $lastInsertId = MigrationCom::max('id');
        }

        $nextId = ($lastInsertId !== null) ? ($lastInsertId + 1) : 1;

        return $nextId;
    }
}

