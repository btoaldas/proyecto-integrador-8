<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = env('PYTHON_AI_SERVICE_URL', 'http://python-ai:8000');
    }
    
    /**
     * Transcribe audio file to text
     */
    public function transcribeAudio(string $audioFilePath): array
    {
        try {
            $response = Http::attach(
                'audio', file_get_contents($audioFilePath), basename($audioFilePath)
            )->post($this->baseUrl . '/transcribe');
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('Error en la transcripción: ' . $response->body());
            
        } catch (\Exception $e) {
            Log::error('AI Service transcription error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Generate document from transcribed text
     */
    public function generateDocument(string $text, string $documentType = 'acta'): array
    {
        try {
            $response = Http::post($this->baseUrl . '/generate-document', [
                'text' => $text,
                'type' => $documentType,
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('Error en la generación del documento: ' . $response->body());
            
        } catch (\Exception $e) {
            Log::error('AI Service document generation error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Check if AI service is available
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('AI Service health check failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get AI service status
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/health');
            
            if ($response->successful()) {
                return [
                    'status' => 'online',
                    'data' => $response->json(),
                ];
            }
            
            return [
                'status' => 'error',
                'message' => 'Service responded with error: ' . $response->status(),
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'offline',
                'message' => $e->getMessage(),
            ];
        }
    }
}