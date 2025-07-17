<?php

namespace App\Src\Infrastructure\Controllers\Backoffice;

use Illuminate\Http\Request;
use App\Src\Infrastructure\Controllers\Backoffice\ApiResourceController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogErrorController extends ApiResourceController
{
    public function __invoke(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => ['required', 'string'],
                'stack' => ['nullable', 'string'],
                'url' => ['required', 'url'],
                'timestamp' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return $this->respond(
                    $request,
                    [
                        'message' => 'Invalid log content. Error 400',
                        'errors' => $validator->errors()
                    ],
                );
            }
            $logData = $request->only(['message', 'url', 'timestamp', 'stack']);
            $logContent = "";
            foreach ($logData as $key => $value) {
                $logContent .= "{$key}: " . (is_string($value) ? $value : json_encode($value)) . PHP_EOL;
            }
            $currentDate = now()->format('Y-m-d');
            $logMessage = "[{$currentDate} {$request->ip()}]: " . PHP_EOL . $logContent;

            Log::channel('custom_api_log')->info($logMessage);

            return $this->respond($request, [
                'info' => 'Error logged successfully'
            ]);
        } catch (\Exception $e) {
            Log::channel('custom_api_log')->error("Unexpected error: {$e->getMessage()}");
            return $this->respond($request, [
                'message' => 'An unexpected error occurred while logging the error. Error 500',
                'error' => $e->getMessage()
            ]);
        }
    }
}
