<?php

namespace App\Http\Controllers;

use App\Models\LogModel;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the logs.
     */
    public function index(Request $request)
    {
        // Hanya admin yang bisa melihat logs
        if (!$request->user() || $request->user()->role !== 'admin') {
            return ApiFormatter::forbidden('Access denied');
        }
        
        $logs = LogModel::orderBy('created_at', 'desc')->paginate(20);
        
        return ApiFormatter::success($logs);
    }
    
    /**
     * Display the specified log.
     */
    public function show(Request $request, $id)
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return ApiFormatter::forbidden('Access denied');
        }
        
        $log = LogModel::find($id);
        
        if (!$log) {
            return ApiFormatter::notFound('Log not found');
        }
        
        return ApiFormatter::success($log);
    }
    
    /**
     * Clear old logs (older than 30 days).
     */
    public function clearOldLogs(Request $request)
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return ApiFormatter::forbidden('Access denied');
        }
        
        $deleted = LogModel::where('created_at', '<', now()->subDays(30))->delete();
        
        return ApiFormatter::success(null, "Deleted {$deleted} old logs");
    }
}