<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\AuditLog;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
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
        
        return view('documents.index', compact('documents'));
    }
    
    public function create()
    {
        return view('documents.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'document_type' => 'required|in:acta,resolution,ordinance',
            'session_date' => 'nullable|date',
            'audio_file' => 'nullable|file|mimes:mp3,wav,m4a|max:50000', // 50MB
        ]);
        
        $document = new Document();
        $document->title = $request->title;
        $document->document_type = $request->document_type;
        $document->session_date = $request->session_date;
        $document->status = Document::STATUS_DRAFT;
        $document->created_by = Auth::id();
        
        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            $audioPath = $request->file('audio_file')->store('audio', 'local');
            $document->audio_file_path = $audioPath;
        }
        
        $document->save();
        
        AuditLog::logAction(Auth::id(), $document->id, 'created', null, $request);
        
        return redirect()->route('documents.show', $document)
                        ->with('success', 'Documento creado exitosamente.');
    }
    
    public function show(Document $document)
    {
        // Check permissions
        if (!$this->canViewDocument($document)) {
            abort(403, 'No tienes permisos para ver este documento.');
        }
        
        $document->load(['creator', 'reviewer', 'approver', 'signatures.user', 'revisions.revisor']);
        
        return view('documents.show', compact('document'));
    }
    
    public function edit(Document $document)
    {
        if (!$document->canBeEditedBy(Auth::user())) {
            abort(403, 'No tienes permisos para editar este documento.');
        }
        
        return view('documents.edit', compact('document'));
    }
    
    public function update(Request $request, Document $document)
    {
        if (!$document->canBeEditedBy(Auth::user())) {
            abort(403, 'No tienes permisos para editar este documento.');
        }
        
        $request->validate([
            'title' => 'required|string|max:500',
            'content' => 'nullable|string',
            'session_date' => 'nullable|date',
        ]);
        
        $oldContent = $document->content;
        
        $document->update([
            'title' => $request->title,
            'content' => $request->content,
            'session_date' => $request->session_date,
        ]);
        
        // Create revision if content changed
        if ($oldContent !== $request->content) {
            $document->revisions()->create([
                'revision_number' => $document->revisions()->count() + 1,
                'content' => $request->content,
                'changes_summary' => $request->changes_summary ?? 'Contenido actualizado',
                'revised_by' => Auth::id(),
            ]);
        }
        
        AuditLog::logAction(Auth::id(), $document->id, 'updated', ['old_content' => $oldContent], $request);
        
        return redirect()->route('documents.show', $document)
                        ->with('success', 'Documento actualizado exitosamente.');
    }
    
    public function transcribe(Request $request, Document $document)
    {
        if (!$document->audio_file_path) {
            return back()->with('error', 'No hay archivo de audio para transcribir.');
        }
        
        try {
            $audioPath = Storage::path($document->audio_file_path);
            $transcription = $this->aiService->transcribeAudio($audioPath);
            
            $document->update([
                'transcription_text' => $transcription['text'],
            ]);
            
            AuditLog::logAction(Auth::id(), $document->id, 'transcribed', null, $request);
            
            return back()->with('success', 'Transcripción completada exitosamente.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al transcribir el audio: ' . $e->getMessage());
        }
    }
    
    public function generateDocument(Request $request, Document $document)
    {
        if (!$document->transcription_text) {
            return back()->with('error', 'No hay transcripción disponible para generar el documento.');
        }
        
        try {
            $generatedContent = $this->aiService->generateDocument(
                $document->transcription_text,
                $document->document_type
            );
            
            $document->update([
                'content' => $generatedContent['formatted_text'],
            ]);
            
            AuditLog::logAction(Auth::id(), $document->id, 'generated', null, $request);
            
            return back()->with('success', 'Documento generado exitosamente.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el documento: ' . $e->getMessage());
        }
    }
    
    public function publicRepository()
    {
        $documents = Document::where('status', Document::STATUS_PUBLISHED)
                           ->where('is_public', true)
                           ->with('creator')
                           ->orderBy('session_date', 'desc')
                           ->paginate(20);
        
        return view('documents.public-repository', compact('documents'));
    }
    
    public function showPublic(Document $document)
    {
        if (!$document->isPublished()) {
            abort(404, 'Documento no encontrado o no disponible públicamente.');
        }
        
        return view('documents.public-show', compact('document'));
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