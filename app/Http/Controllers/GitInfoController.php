<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class GitInfoController extends Controller
{
    public function index()
    {
        try {
            // Get current branch
            $branch = trim(Process::run('git branch --show-current')->output());

            // Get current commit hash (short)
            $commit = trim(Process::run('git rev-parse --short HEAD')->output());

            // Get current commit message
            $message = trim(Process::run('git log -1 --pretty=%s')->output());

            // Version can be from config or package.json
            $version = config('app.version', '1.0.0');

            return response()->json([
                'branch' => $branch ?: 'unknown',
                'commit' => $commit ?: 'unknown',
                'message' => $message ?: 'No commit message',
                'version' => $version
            ]);
        } catch (\Exception $e) {
            // Fallback in case git commands fail
            return response()->json([
                'branch' => 'unknown',
                'commit' => 'unknown',
                'message' => 'Git info unavailable',
                'version' => config('app.version', '1.0.0')
            ]);
        }
    }
}