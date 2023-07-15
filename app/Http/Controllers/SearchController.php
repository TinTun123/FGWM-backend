<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Campagin;
use App\Models\News;
use App\Models\Protests;
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
            Log::info('campagin', [
                $result
            ]);

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

            return response()->json($result, 200);
        }



    }
}
