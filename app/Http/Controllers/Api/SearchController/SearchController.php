<?php

namespace App\Http\Controllers\Api\SearchController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    // Common searchable column names
    private $searchableColumns = ['title', 'name', 'content', 'description'];

    // Tables to exclude from search
    private $excludedTables = [
        'migrations',
        'cache',
        'cache_locks',
        'jobs',
        'failed_jobs',
        'personal_access_tokens',
        'password_reset_tokens',
        'sessions',
    ];

    // Limit results per table
    private $limitPerTable = 5;

    // Minimum query length
    private $minQueryLength = 2;

    public function search(Request $request)
    {
        $query = trim($request->input('q'));

        // Validate minimum length
        if (strlen($query) < $this->minQueryLength) {
            return response()->json([
                'success' => false,
                'message' => "Please enter at least {$this->minQueryLength} characters."
            ], 400);
        }

        // Cache results for 2 minutes
        $cacheKey = 'search:' . md5($query);
        
        $data = Cache::remember($cacheKey, 120, function () use ($query) {
            return $this->performSearch($query);
        });

        return response()->json([
            'success' => true,
            'query' => $query,
            'total_tables_scanned' => $data['tables_scanned'],
            'matched_tables' => count($data['results']),
            'total_results' => array_sum(array_column($data['results'], 'count')),
            'results' => $data['results'],
        ]);
    }

    private function performSearch($query)
    {
        $results = [];
        $searchTerm = "%{$query}%";
        $dbName = DB::getDatabaseName();
        $tables = DB::select("SHOW TABLES");
        $tableKey = "Tables_in_{$dbName}";
        $tablesScanned = 0;

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey;

            // Skip excluded tables
            if ($this->shouldSkipTable($table)) {
                continue;
            }

            $tablesScanned++;

            try {
                // Get all columns in the table
                $allColumns = Schema::getColumnListing($table);
                
                if (empty($allColumns)) {
                    continue;
                }

                // Find which searchable columns exist in this table
                $columnsToSearch = array_intersect($this->searchableColumns, $allColumns);

                // Skip table if it has none of the searchable columns
                if (empty($columnsToSearch)) {
                    continue;
                }

                // Build query only for searchable columns
                $rows = DB::table($table)
                    ->where(function ($q) use ($columnsToSearch, $searchTerm) {
                        foreach ($columnsToSearch as $column) {
                            $q->orWhere($column, 'LIKE', $searchTerm);
                        }
                    })
                    ->limit($this->limitPerTable)
                    ->get();

                if ($rows->count() > 0) {
                    $results[$table] = [
                        'count' => $rows->count(),
                        'searched_columns' => array_values($columnsToSearch),
                        'data' => $rows
                    ];
                }
            } catch (\Exception $e) {
                // Skip problematic tables gracefully
                \Log::warning("Search error on table {$table}: " . $e->getMessage());
                continue;
            }
        }

        return [
            'results' => $results,
            'tables_scanned' => $tablesScanned
        ];
    }

    private function shouldSkipTable($table)
    {
        // Check excluded tables
        foreach ($this->excludedTables as $excluded) {
            if (Str::contains($table, $excluded)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get total count without fetching data (for pagination/stats)
     */
    public function searchCount(Request $request)
    {
        $query = trim($request->input('q'));

        if (strlen($query) < $this->minQueryLength) {
            return response()->json(['success' => false], 400);
        }

        $cacheKey = 'search_count:' . md5($query);
        
        $counts = Cache::remember($cacheKey, 120, function () use ($query) {
            $counts = [];
            $searchTerm = "%{$query}%";
            $dbName = DB::getDatabaseName();
            $tables = DB::select("SHOW TABLES");
            $tableKey = "Tables_in_{$dbName}";

            foreach ($tables as $tableObj) {
                $table = $tableObj->$tableKey;

                if ($this->shouldSkipTable($table)) {
                    continue;
                }

                try {
                    $allColumns = Schema::getColumnListing($table);
                    $columnsToSearch = array_intersect($this->searchableColumns, $allColumns);

                    if (empty($columnsToSearch)) {
                        continue;
                    }

                    $count = DB::table($table)
                        ->where(function ($q) use ($columnsToSearch, $searchTerm) {
                            foreach ($columnsToSearch as $column) {
                                $q->orWhere($column, 'LIKE', $searchTerm);
                            }
                        })
                        ->count();

                    if ($count > 0) {
                        $counts[$table] = $count;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $counts;
        });

        return response()->json([
            'success' => true,
            'counts' => $counts,
            'total' => array_sum($counts)
        ]);
    }

    /**
     * Clear search cache
     */
    public function clearCache()
    {
        Cache::forget('search:*');
        return response()->json(['success' => true, 'message' => 'Search cache cleared']);
    }
}