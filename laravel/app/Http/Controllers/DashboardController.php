<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Statistics
        $stats = [
            'total_documents' => Document::count(),
            'draft_documents' => Document::where('status', Document::STATUS_DRAFT)->count(),
            'pending_review' => Document::where('status', Document::STATUS_REVIEW)->count(),
            'published_documents' => Document::where('status', Document::STATUS_PUBLISHED)->count(),
            'total_users' => User::where('is_active', true)->count(),
        ];
        
        // Recent documents based on user role
        $recentDocuments = $this->getRecentDocumentsForUser($user);
        
        // Recent activity
        $recentActivity = AuditLog::with(['user', 'document'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Pending tasks for the user
        $pendingTasks = $this->getPendingTasksForUser($user);
        
        return view('dashboard', compact('stats', 'recentDocuments', 'recentActivity', 'pendingTasks'));
    }
    
    private function getRecentDocumentsForUser($user)
    {
        $query = Document::with(['creator', 'reviewer', 'approver'])
            ->orderBy('updated_at', 'desc');
        
        if ($user->isViewer()) {
            // Viewers can only see published documents
            $query->where('status', Document::STATUS_PUBLISHED)
                  ->where('is_public', true);
        } elseif ($user->isSecretary()) {
            // Secretaries can see their own documents and published ones
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('status', Document::STATUS_PUBLISHED);
            });
        }
        // Admins and reviewers can see all documents
        
        return $query->limit(5)->get();
    }
    
    private function getPendingTasksForUser($user)
    {
        $tasks = [];
        
        if ($user->isReviewer() || $user->isAdmin()) {
            $pendingReviews = Document::where('status', Document::STATUS_REVIEW)->count();
            if ($pendingReviews > 0) {
                $tasks[] = [
                    'type' => 'review',
                    'count' => $pendingReviews,
                    'message' => "Tienes {$pendingReviews} documento(s) pendiente(s) de revisión",
                    'url' => route('documents.index', ['status' => Document::STATUS_REVIEW])
                ];
            }
        }
        
        if ($user->isSecretary() || $user->isAdmin()) {
            $pendingPublish = Document::where('status', Document::STATUS_APPROVED)->count();
            if ($pendingPublish > 0) {
                $tasks[] = [
                    'type' => 'publish',
                    'count' => $pendingPublish,
                    'message' => "Tienes {$pendingPublish} documento(s) aprobado(s) pendiente(s) de publicación",
                    'url' => route('documents.index', ['status' => Document::STATUS_APPROVED])
                ];
            }
        }
        
        return $tasks;
    }
}