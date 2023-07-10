<?php

namespace App\Http\Controllers;

// use App\Jobs\SendNewArticleNotificationJob;
// use App\Mail\NewArticleNotification;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Campagin;
use App\Models\MigrationCom;
use App\Models\News;
use App\Models\Protests;
use App\Models\Subscribe;
use App\Models\User;
use App\Models\Women;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    
    public function create(Request $request) {
        Log::info('$request->committes', [
            $request->input('committees')
        ]);
        $request->validate([
           'title' => 'required|min:3|max:255',
           'date' => 'required|date',
           'coverImg' => 'required|file|mimetypes:image/jpeg,image/png,video/mp4',
           'committees' => 'required|array|in:fgwm,women,migration'
        ]);

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

            } elseif ($type === 'news') {
                $article = new News;
            }

            $user_id = Auth::id();
            $article->id = $this->generateNewId($type);
            $article->title = $request->input('title');
            $article->date = $request->input('date');
            $article->committees = implode(',', $request->input('committees'));
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
                $storeThumbs =  $this->storeThumbnails($images, $article->id, $request->input('type'));

                if(!$storeThumbs) {
                    return response()->json(['error' => 'Saving files not success'], 500);
                }

            }

            if ($request->hasFile('thumbnail')) {
                
                $thumbnail = $this->storeImage($request->file('thumbnail'), $article->id . '/videoThumb', $request->input('type'));
                
                if ($thumbnail === '') {
                    return response()->json(['error' => 'Saving file not complete'], 500);
                }
            }
    
            $article->save();

            $article->user = User::find($user_id);

            $temp = $this->isVideo($type, $article->id, basename($article->imgURL));
            $article['isVideo'] = $temp[1];
            $article['isProtrait'] = $temp[0];
            $article['thumbnail'] = $temp[2];
            

            $subscribeEmails = Subscribe::pluck('email')->all();

            // dispatch(new SendNewArticleNotificationJob($article, $subscribeEmails));


            return response()->json([
                $article
            ], 200);

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

                $protests = Protests::withCount('messages')->with('user')->orderByDesc('date')->get()->toArray();
                if ($protests) {

                    foreach ($protests as &$protest) {

                        $protest['created_at'] = Carbon::parse($protest['created_at'])->format('d M Y');
                        $temp = $this->isVideo($type, $protest['id'], basename($protest['imgURL']));
                        $protest['isVideo'] = $temp[1];
                        $protest['isProtrait'] = $temp[0];
                        $protest['thumbnail'] = $temp[2];
        
                    }
                }    

                    
                
                return response()->json($protests);

            } else if ($type === 'activities') {

                $activities = Activity::withCount('messages')->with('user')->orderByDesc('date')->get()->toArray();
                Log::info('activities', [
                    $activities
                ]);
                if($activities) {
                    foreach ($activities as &$activity) {
                        $activity['created_at'] = Carbon::parse($activity['created_at'])->format('d M Y');
                        $temp = $this->isVideo($type, $activity['id'], basename($activity['imgURL']));
                        $activity['isVideo'] = $temp[1];
                        $activity['isProtrait'] = $temp[0];
                        $activity['thumbnail'] = $temp[2];
                    }
                }


                return response()->json($activities);

            } else if ($type === 'articles') {

                $articles = Article::withCount('messages')->with('user')->orderByDesc('date')->get()->toArray();
                if($articles) {
                    foreach ($articles as &$article) {
                        $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
    
                        $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                        $article['isVideo'] = $temp[1];
                        $article['isProtrait'] = $temp[0];
                        $article['thumbnail'] = $temp[2];
                    }
                }

                return response()->json($articles);

            } else if ($type === 'campagins') {
                $articles = Campagin::withCount('messages')->with('user')->orderByDesc('date')->get()->toArray();

                if($articles) {
                    foreach ($articles as &$article) {
                        $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                        $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                        $article['isVideo'] = $temp[1];
                        $article['isProtrait'] = $temp[0];
                        $article['thumbnail'] = $temp[2];
                    }
                }

                return response()->json($articles);
            } else if ($type === 'women') {
                $articles = Women::withCount('messages')->with('user')->orderByDesc('date')->get()->toArray();
                if($articles) {
                    foreach ($articles as &$article) {
                        $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                        $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                        $article['isVideo'] = $temp[1];
                        $article['isProtrait'] = $temp[0];
                        $article['thumbnail'] = $temp[2];
                    }
                }


                return response()->json($articles);
            } else if ($type === 'migration') {
                $articles = MigrationCom::withCount('messages')->with('user')->orderByDesc('date')->get()->toArray();

                if($articles) {
                    foreach ($articles as &$article) {
                        $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                        $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                        $article['isVideo'] = $temp[1];
                        $article['isProtrait'] = $temp[0];
                        $article['thumbnail'] = $temp[2];
                    }
                }
                return response()->json($articles);
            } else if ($type === 'news') {
                $articles = News::withCount('messages')->with('user')->orderByDesc('date')->get()->toArray();

                if($articles) {
                    foreach($articles as &$article) {
                        $article['created_at'] = Carbon::parse($article['created_at'])->format('d M Y');
                        $temp = $this->isVideo($type, $article['id'], basename($article['imgURL']));
                        $article['isVideo'] = $temp[1];
                        $article['isProtrait'] = $temp[0];
                        $article['thumbnail'] = $temp[2];
                    }
                }
                return response()->json($articles);
            }

        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage(),
                500
            ]);
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
            } elseif ($type === 'news') {
                $article = News::with('user')->find($id);
            }



            $user_id = Auth::id();

            if(!$article) {
                return response()->json(['error' => 'Article not found'], 500);
            }

            $title = $request->input('title');

            if(!$title) {
                return response()->json([
                    'error' => 'No title found',
                    500
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
            
            $committees = implode(',', $request->input('committees'));
            if ($committees) {
                $article->committees = $committees;
            }

            if($request->hasFile('coverImg')) {

                $article->imgURL = $this->storeImage($request->file('coverImg'), $id, $type);

                if($article->imgURL == '') {
                    return response()->json(['error' => 'Saving file not complete'], 500);
                }

            }
            $folderPath = public_path('images/' . $type . '/' . $id . '/' . 'thumbnails/');
            if(File::exists($folderPath)) {
                File::deleteDirectory($folderPath);
            }

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $storeThumbs = $this->storeThumbnails($images, $id, $request->input('type'));

                if(!$storeThumbs) {
                    return response()->json(['error' => 'Saving files not complete'], 500);
                }
            }

            if ($request->hasFile('thumbnail')) {
                
                $storeVideoThumb = $this->storeImage($request->file('thumbnail'), $id . '/videoThumb', $type);

                if($storeVideoThumb == '') {
                    return response()->json(['error' => 'Saving file not complete'], 500);
                }
            
            }

            $article->save();
            $temp = $this->isVideo($type, $id, basename($article->imgURL));
            $article['isVideo'] = $temp[1];
            $article['isProtrait'] = $temp[0];
            $article['thumbnail'] = $temp[2];
            return response()->json([$article]);

        } catch (Exception $ece) {
            return response()->json([
                'error' => $ece->getMessage(),
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

            } elseif ($type === 'women') {

                $article = Women::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = Women::latest()->first();

            } elseif ($type === 'migration') {

                $article = MigrationCom::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = MigrationCom::latest()->first();

            } elseif ($type === 'news') {
                $article = News::findOrFail($id);
                $article->messages()->delete();
                $article->delete();
                $lastProtest = News::latest()->first();
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
                $exc,
                500
            ]);
        }
    }


    public function showThumbnails(Request $request, $type, $committees, $id) {

        $folderPath = 'images/' . $type . '/' . $id . '/thumbnails';
        
        if(!file_exists(public_path($folderPath)) || !is_dir(public_path($folderPath))) {
            return response()->json([]);
        }

        if ($type === 'protest') {

            $article = Protests::findOrFail($id);
            Log::info('is_contain', [
                Str::contains($article->committees, $committees)
            ]);
            if(!Str::contains($article->committees, $committees)) {
                return response()->json([]);
            }

        } elseif ($type === 'activities') {

            $article = Activity::findOrFail($id);
            if(!Str::contains($article->committees, $committees)) {
                return response()->json([]);
            }

        } elseif ($type === 'articles') {

            $article = Article::findOrFail($id);
            if(!Str::contains($article->committees, $committees)) {
                return response()->json([]);
            }

        } elseif ($type === 'campagins') {

            $article = Campagin::findOrFail($id);
            if(!Str::contains($article->committees, $committees)) {
                return response()->json([]);
            }

        } elseif ($type === 'women') {

            $article = Women::findOrFail($id);

        } elseif ($type === 'migration') {

            $article = MigrationCom::findOrFail($id);

        } elseif ($type === 'news') {
            $article = News::findOrFail($id);
            if(!Str::contains($article->committees, $committees)) {
                return response()->json([]);
            }
        }

        $files = scandir(public_path($folderPath));
        $links = [];

        foreach($files as $file) {

            if($file !== '.' && $file !== '..') {
                $publicUrl = asset($folderPath . '/' . $file);
                $links[] = $publicUrl;
            }

        }


        $article->increment('total_view');


        return response()->json($links);

    }

    private function storeImage($image, $protetId, $type) {

        try {
            $imageExtension = ['jpg', 'jpeg', 'png', 'gif'];
            $videoExtension = ['mp4', 'mov', 'avi', 'wmv', 'flv'];
            $fileextension = strtolower($image->getClientOriginalExtension());
            $folderPath = 'images/' . $type . '/' . $protetId;
    
            Storage::disk('public')->deleteDirectory($folderPath);
    
    
    
    
            
    
            if(!File::exists(public_path($folderPath))) {
    
                File::makeDirectory(public_path($folderPath), 0777, true, true);
    
            }
    
    
            if(in_array($fileextension, $imageExtension) || in_array($fileextension, $videoExtension)) {
                // it is image fo something
                try {
                    $path = $image->store($folderPath, 'public');
                } catch(\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
    
    
    
            // 
            $publicURL = asset($path);
    
            return $publicURL;
        } catch (Exception $ex) {
            return '';
        }
    }

    // private function compressVideo($videoPath, $outputPath) {

    //     // $commad = '/usr/bin/ffmpeg' -i ' . $videoPath . ' -vcodec libx264 -crf 28 ' . $outputPath;
    //     $commad = '/usr/bin/ffmpeg';        
    //     $process = new Process([
    //         $commad,
    //         '-i',
    //         $videoPath,
    //         '-r',
    //         '20',
    //         '-c:v',
    //         'libx264',
    //         '-b:v',
    //         '600k',
    //         '-b:a',
    //         '44100',
    //         '-ac',
    //         '2',
    //         '-ar',
    //         '22050',
    //         '-tune',
    //         'fastdecode',
    //         $outputPath
    //     ]);

    //     try {
    //         $process->run(function ($type, $buffer) {
    //             Log::info($buffer);
    //         });

    //         if($process->isSuccessful()) {
    //             $output = $process->getOutput();
    //             Log::info('command output' . $output);
    //             return true;
    //         } else {
    //             Log::info('shit went down');
    //             return false;
    //         }
    //     } catch (ProcessFailedException $exce) {
    //         Log::info($exce);
    //         return false;
    //     }
    // }

    private function storeThumbnails($images, $protestId, $type) {

        try {
            //code...
            $folderPath = public_path('images/' . $type . '/' . $protestId . '/' . 'thumbnails/');

            if(!File::exists($folderPath)) {
    
                File::makeDirectory($folderPath, 0777, true, true);
            }
    
            foreach ($images as $image) {
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($folderPath, $filename);
            }
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
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
        } elseif ($type === 'news') {
            $lastInsertId = News::max('id');
        }

        $nextId = ($lastInsertId !== null) ? ($lastInsertId + 1) : 1;

        return $nextId;
    }
}

