<?php

namespace App\Http\Controllers\Api\SearchController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('q'));

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a search query.'
            ], 400);
        }

        // 🔹 Keywords
        $keywords = array_filter(explode(' ', $query));

        // 🔹 Get all tables in DB
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $key = "Tables_in_{$dbName}";
        $results = [];

        foreach ($tables as $tableObj) {
            $table = $tableObj->$key;

            // ⏭️ Skip migration or cache tables
            if (Str::contains($table, ['migrations', 'password', 'cache', 'jobs'])) continue;

            // 🔍 Get string-like columns only
            $columns = collect(Schema::getColumnListing($table))
                ->filter(fn($col) => !Str::contains($col, ['id', 'created_at', 'updated_at', 'deleted_at']));

            if ($columns->isEmpty()) continue;

            // 🔹 Build the query
            $results[$table] = DB::table($table)
                ->where(function ($q) use ($columns, $keywords) {
                    foreach ($keywords as $keyword) {
                        $q->where(function ($subQuery) use ($columns, $keyword) {
                            foreach ($columns as $column) {
                                $subQuery->orWhere($column, 'LIKE', "%{$keyword}%");
                            }
                        });
                    }
                })
                ->limit(10) // safety limit for performance
                ->get();
        }

        $filteredResults = collect($results)->filter(fn($r) => $r->count() > 0);

        return response()->json([
            'success' => true,
            'query' => $query,
            'empty' => $filteredResults->isEmpty(),
            'total_results' => $filteredResults->sum(fn($r) => $r->count()),
            'results' => $filteredResults,
        ]);
    }
}
