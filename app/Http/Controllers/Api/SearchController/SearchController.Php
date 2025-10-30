<?php

namespace App\Http\Controllers\Api\SearchController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

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

        $keywords = array_filter(explode(' ', $query));
        $dbName = DB::getDatabaseName();
        $tables = DB::select("SHOW TABLES");
        $tableKey = "Tables_in_{$dbName}";
        $results = [];

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey;

            // Skip irrelevant or system tables
            if (Str::contains($table, ['migrations', 'cache', 'jobs', 'failed_jobs', 'personal_access_tokens'])) {
                continue;
            }

            try {
                $columns = Schema::getColumnListing($table);

                if (empty($columns)) {
                    continue;
                }

                // Build flexible query for all columns
                $queryBuilder = DB::table($table);

                $queryBuilder->where(function ($outerQuery) use ($columns, $keywords) {
                    foreach ($keywords as $keyword) {
                        $outerQuery->where(function ($subQuery) use ($columns, $keyword) {
                            foreach ($columns as $column) {
                                $subQuery->orWhere($column, 'LIKE', "%{$keyword}%");
                            }
                        });
                    }
                });

                $rows = $queryBuilder->limit(15)->get();

                if ($rows->count() > 0) {
                    $results[$table] = [
                        'count' => $rows->count(),
                        'data' => $rows
                    ];
                }
            } catch (QueryException $e) {
                // Skip problematic tables gracefully
                continue;
            } catch (\Exception $e) {
                continue;
            }
        }

        return response()->json([
            'success' => true,
            'query' => $query,
            'total_tables_scanned' => count($tables),
            'matched_tables' => count($results),
            'total_results' => collect($results)->sum(fn($r) => $r['count']),
            'results' => $results,
        ]);
    }
}
