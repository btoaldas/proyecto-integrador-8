<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\AuditLog;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentApiController extends Controller
{
    protected $aiService;
    
    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Document::with(['creator', 'reviewer', 'approver']);
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by document type
        if ($request->has('type')) {
            $query->where('document_type', $request->type);
        }
        
        // Filter by user role
        if ($user->isViewer()) {
            $query->where('status', Document::STATUS_PUBLISHED)
                  ->where('is_public', true);
        } elseif ($user->isSecretary()) {
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('status', Document::STATUS_PUBLISHED);
            });
        }
        
        $documents = $query->orderBy('updated_at', 'desc')->paginate(15);
        
        return response()->json($documents);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'document_type' => 'required|in:acta,resolution,ordinance',
            'session_date' => 'nullable|date',
            'content' => 'nullable|string',
        ]);
        
        $document = Document::create([
            'title' => $request->title,
            'document_type' => $request->document_type,
            'session_date' => $request->session_date,
            'content' => $request->content,
            'status' => Document::STATUS_DRAFT,
            'created_by' => Auth::id(),
        ]);
        
        AuditLog::logAction(Auth::id(), $document->id, 'created', null, $request);
        
        return response()->json($document->load(['creator']), 201);
    }
    
    public function show(Document $document)
    {
        // Check permissions (simplified for API)
        if (!$this->canViewDocument($document)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($document->load(['creator', 'reviewer', 'approver', 'signatures.user']));
    }
    
    public function update(Request $request, Document $document)
    {
        if (!$document->canBeEditedBy(Auth::user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'title' => 'required|string|max:500',
            'content' => 'nullable|string',
            'session_date' => 'nullable|date',
        ]);
        
        $document->update($request->only(['title', 'content', 'session_date']));
        
        AuditLog::logAction(Auth::id(), $document->id, 'updated', null, $request);
        
        return response()->json($document->load(['creator']));
    }
    
    public function destroy(Document $document)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        AuditLog::logAction(Auth::id(), $document->id, 'deleted', null, request());
        $document->delete();
        
        return response()->json(['message' => 'Document deleted successfully']);
    }
    
    public function publicDocuments()
    {
        $documents = Document::where('status', Document::STATUS_PUBLISHED)
                           ->where('is_public', true)
                           ->with('creator')
                           ->orderBy('session_date', 'desc')
                           ->paginate(20);
        
        return response()->json($documents);
    }
    
    public function showPublic(Document $document)
    {
        if (!$document->isPublished()) {
            return response()->json(['error' => 'Document not found'], 404);
        }
        
        return response()->json($document->load(['creator']));
    }
    
    public function transcribe(Request $request, Document $document)
    {
        if (!$document->audio_file_path) {
            return response()->json(['error' => 'No audio file available'], 400);
        }
        
        try {
            // Mock transcription for API demo
            $transcription = [
                'text' => 'Transcripción simulada del documento de audio',
                'language' => 'es',
                'confidence' => 0.95
            ];
            
            $document->update([
                'transcription_text' => $transcription['text'],
            ]);
            
            AuditLog::logAction(Auth::id(), $document->id, 'transcribed', null, $request);
            
            return response()->json([
                'message' => 'Transcription completed',
                'transcription' => $transcription
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Transcription failed'], 500);
        }
    }
    
    public function generateDocument(Request $request, Document $document)
    {
        if (!$document->transcription_text) {
            return response()->json(['error' => 'No transcription available'], 400);
        }
        
        try {
            // Mock document generation
            $generatedContent = "Documento generado automáticamente:\n\n" . $document->transcription_text;
            
            $document->update([
                'content' => $generatedContent,
            ]);
            
            AuditLog::logAction(Auth::id(), $document->id, 'generated', null, $request);
            
            return response()->json([
                'message' => 'Document generated successfully',
                'content' => $generatedContent
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Document generation failed'], 500);
        }
    }
    
    public function sign(Request $request, Document $document)
    {
        // Mock digital signature
        $document->signatures()->create([
            'user_id' => Auth::id(),
            'signature_hash' => hash('sha256', $document->content . Auth::id() . now()),
            'status' => 'signed',
            'signature_timestamp' => now(),
        ]);
        
        AuditLog::logAction(Auth::id(), $document->id, 'signed', null, $request);
        
        return response()->json(['message' => 'Document signed successfully']);
    }
    
    public function publish(Request $request, Document $document)
    {
        if (!$document->canBePublishedBy(Auth::user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $document->update([
            'status' => Document::STATUS_PUBLISHED,
            'is_public' => true,
        ]);
        
        AuditLog::logAction(Auth::id(), $document->id, 'published', null, $request);
        
        return response()->json(['message' => 'Document published successfully']);
    }
    
    private function canViewDocument(Document $document)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return true;
        }
        
        if ($document->isPublished()) {
            return true;
        }
        
        if ($document->created_by === $user->id) {
            return true;
        }
        
        if ($user->isReviewer() && $document->status === Document::STATUS_REVIEW) {
            return true;
        }
        
        return false;
    }
}