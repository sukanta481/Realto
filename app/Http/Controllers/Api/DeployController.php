<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    /**
     * Handle GitHub webhook for auto-deployment
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deploy(Request $request)
    {
        // Get the GitHub secret from environment
        $secret = env('GITHUB_WEBHOOK_SECRET');
        
        // Verify the webhook signature if secret is set
        if ($secret) {
            $signature = $request->header('X-Hub-Signature-256');
            
            if (!$signature) {
                Log::warning('Deployment webhook called without signature');
                return response()->json(['message' => 'No signature provided'], 401);
            }
            
            $payload = $request->getContent();
            $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
            
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Deployment webhook signature mismatch');
                return response()->json(['message' => 'Invalid signature'], 401);
            }
        }
        
        // Get the event type
        $event = $request->header('X-GitHub-Event');
        
        // Only process push events
        if ($event !== 'push') {
            Log::info("Deployment webhook received non-push event: {$event}");
            return response()->json(['message' => 'Event ignored'], 200);
        }
        
        // Get the branch from payload
        $payload = json_decode($request->getContent(), true);
        $branch = str_replace('refs/heads/', '', $payload['ref'] ?? '');
        
        // Only deploy from main/master branch (adjust as needed)
        $targetBranch = env('DEPLOY_BRANCH', 'main');
        
        if ($branch !== $targetBranch) {
            Log::info("Deployment webhook: Push to {$branch} branch ignored");
            return response()->json(['message' => "Only {$targetBranch} branch triggers deployment"], 200);
        }
        
        Log::info("Deployment started from GitHub webhook for branch: {$branch}");
        
        // Path to the deployment script
        $scriptPath = base_path('deploy.sh');
        
        if (!file_exists($scriptPath)) {
            Log::error('Deployment script not found at: ' . $scriptPath);
            return response()->json(['message' => 'Deployment script not found'], 500);
        }
        
        // Execute the deployment script in the background
        $output = [];
        $returnCode = 0;
        
        // For Linux/Unix servers
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            exec("bash {$scriptPath} > /dev/null 2>&1 &", $output, $returnCode);
        } else {
            // For Windows (if testing locally)
            exec("start /B {$scriptPath}", $output, $returnCode);
        }
        
        if ($returnCode === 0) {
            Log::info('Deployment script executed successfully');
            return response()->json([
                'message' => 'Deployment initiated successfully',
                'branch' => $branch,
                'timestamp' => now()->toDateTimeString()
            ], 200);
        } else {
            Log::error('Deployment script execution failed with code: ' . $returnCode);
            return response()->json([
                'message' => 'Deployment script execution failed',
                'error_code' => $returnCode
            ], 500);
        }
    }
    
    /**
     * Manual deployment trigger (for testing)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualDeploy()
    {
        Log::info('Manual deployment initiated');
        
        $scriptPath = base_path('deploy.sh');
        
        if (!file_exists($scriptPath)) {
            return response()->json(['message' => 'Deployment script not found'], 500);
        }
        
        $output = [];
        $returnCode = 0;
        
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            exec("bash {$scriptPath} 2>&1", $output, $returnCode);
        } else {
            exec("{$scriptPath} 2>&1", $output, $returnCode);
        }
        
        return response()->json([
            'message' => $returnCode === 0 ? 'Deployment completed' : 'Deployment failed',
            'output' => $output,
            'return_code' => $returnCode
        ], $returnCode === 0 ? 200 : 500);
    }
}
