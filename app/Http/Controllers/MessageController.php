<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Message;
use App\Models\Protests;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                $article = Protests::findOrFail($id);
            } elseif ($type === 'activities') {
                $article = Activity::findOrFail($id);
            } elseif ($type === 'articles') {
                $article = Article::findOrFail($id);
            }
            $messages = $article->messages->toArray();

            foreach ($messages as &$message) {

                $message['created_at'] = Carbon::parse($message['created_at'])->diffForHumans();

            }
            
            return response()->json($messages);
        } catch (Exception $ec) {
            return response()->json($ec,500);
        }
    }

    public function deleteMessage(Request $request, $articleType, $articleid, $msgId) {
        
        if($articleType === 'protest') {
            $article = Protests::findOrFail($articleid);
        } elseif ($articleType === 'activities') {
            $article = Activity::findOrFail($articleid);
        }
        
        $message = $article->messages()->findOrFail($msgId);
        $message->delete();

        return response()->json([
            'message delete successfully',
            200
        ]);
    }
}
