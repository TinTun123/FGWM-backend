<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Campagin;
use App\Models\Message;
use App\Models\MigrationCom;
use App\Models\News;
use App\Models\Protests;
use App\Models\Women;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function saveMessage(Request $request, $type, $id) {

        try {
            
            if ($type === 'protest') {
                $article = Protests::findOrFail($id);
            } elseif ($type === 'activities') {
                $article = Activity::findOrFail($id);
            } elseif ($type === 'articles') {
                $article = Article::findOrFail($id);
            } elseif ($type === 'campagins') {
                $article = Campagin::findOrFail($id);
            } elseif ($type === 'migration') {
                $article = MigrationCom::findOrFail($id);
            } elseif ($type === 'women') {
                $article = Women::findOrFail($id);
            } elseif ($type === 'news') {
                $article =News::findOrFail($id);
            }


            $content = $request->input('msg');
            $name = $request->input('name');           
            if($content && $name) {
                $message = new Message;
                $message->body = $content;
                $message->name = $name;
                $article->messages()->save($message);
            }
            
            return response()->json($article->messages);
        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex,
                500
            ]);
        }


    }

    public function getMessage(Request $request, $type, $id) {
        try {
            if($type === 'protest') {
                $article = Protests::find($id);
            } elseif ($type === 'activities') {
                $article = Activity::find($id);
            } elseif ($type === 'articles') {
                $article = Article::find($id);
            } elseif ($type === 'campagins') {
                $article = Campagin::find($id);
            } elseif ($type === 'women') {
                $article = Women::find($id);
            } elseif ($type === 'migration') {
                $article = MigrationCom::find($id);
            } elseif($type === 'news') {
                $article = News::find($id);
            }

            if ($article) {
                $messages = $article->messages->toArray();

                foreach ($messages as &$message) {
    
                    $message['created_at'] = Carbon::parse($message['created_at'])->diffForHumans();
    
                }
                return response()->json($messages);
            } else {
                return response()->json([]);
            }

            

        } catch (Exception $ec) {
            if ($ec instanceof ModelNotFoundException) {
                return response()->json([], 500);
            }
            return response()->json($ec,500);
        }
    } 

    public function deleteMessage(Request $request, $articleType, $articleid, $msgId) {
        
        if($articleType === 'protest') {
            $article = Protests::findOrFail($articleid);
        } elseif ($articleType === 'activities') {
            $article = Activity::findOrFail($articleid);
        } elseif ($articleType === 'campagins') {
            $article = Campagin::findOrFail($articleid);
        } elseif ($articleType === 'news') {
            $article = News::findOrFail($articleid);
        } elseif ($articleType === 'articles') {
            $article = Article::findOrFail($articleid);
        }
        
        $message = $article->messages()->findOrFail($msgId);
        $message->delete();

        return response()->json([
            'message delete successfully',
            200
        ]);
    }
}
