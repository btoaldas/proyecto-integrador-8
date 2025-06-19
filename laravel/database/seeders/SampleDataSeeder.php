<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating sample data...');
        
        // Obtener usuarios existentes
        $admin = DB::table('users')->where('email', 'admin@municipio.gov')->first();
        $secretary = DB::table('users')->where('email', 'secretario@municipio.gov')->first();
        $reviewer = DB::table('users')->where('email', 'revisor@municipio.gov')->first();
        $viewer = DB::table('users')->where('email', 'publico@municipio.gov')->first();
        
        // 1. Crear documentos de ejemplo
        $documents = [
            [
                'id' => Str::uuid(),
                'title' => 'Acta de Sesión Ordinaria N° 001-2024',
                'document_type' => 'acta',
                'content' => 'En la ciudad de [Ciudad], siendo las 10:00 AM del día 15 de enero del 2024, se reunieron en sesión ordinaria los miembros del Concejo Municipal... Se aprobó por unanimidad el presupuesto anual 2024 por un monto de $2,500,000.00',
                'status' => 'published',
                'session_date' => '2024-01-15',
                'is_public' => true,
                'created_by' => $secretary->id,
                'reviewed_by' => $reviewer->id,
                'approved_by' => $admin->id,
                'transcription_text' => 'Transcripción completa de la sesión ordinaria donde se discutió el presupuesto municipal...',
                'audio_file_path' => '/storage/audio/acta_001_2024.mp3',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Resolución N° 005-2024 - Contratación de Personal',
                'document_type' => 'resolution',
                'content' => 'El Alcalde Municipal, en uso de sus facultades legales... RESUELVE: Aprobar la contratación de 5 nuevos funcionarios para el departamento de obras públicas.',
                'status' => 'approved',
                'session_date' => '2024-02-10',
                'is_public' => false,
                'created_by' => $secretary->id,
                'reviewed_by' => $reviewer->id,
                'approved_by' => $admin->id,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Ordenanza Municipal sobre Gestión de Residuos',
                'document_type' => 'ordinance',
                'content' => 'El Concejo Municipal, considerando la necesidad de mejorar la gestión de residuos sólidos... ORDENA: Implementar un nuevo sistema de recolección selectiva.',
                'status' => 'review',
                'session_date' => '2024-03-05',
                'is_public' => false,
                'created_by' => $secretary->id,
                'transcription_text' => 'Discusión sobre la implementación del nuevo sistema de recolección de residuos...',
                'audio_file_path' => '/storage/audio/ordenanza_residuos_2024.mp3',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(8),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Acta de Sesión Extraordinaria - Emergencia Sanitaria',
                'document_type' => 'acta',
                'content' => 'En sesión extraordinaria convocada debido a la emergencia sanitaria declarada...',
                'status' => 'draft',
                'session_date' => '2024-03-20',
                'is_public' => false,
                'created_by' => $secretary->id,
                'transcription_text' => 'Reunión de emergencia para tratar temas de salud pública...',
                'audio_file_path' => '/storage/audio/sesion_extraordinaria_2024.mp3',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Resolución N° 008-2024 - Aprobación de Convenio Educativo',
                'document_type' => 'resolution',
                'content' => 'Aprobar el convenio de cooperación educativa con la Universidad Nacional para el desarrollo de programas de capacitación ciudadana.',
                'status' => 'published',
                'session_date' => '2024-02-28',
                'is_public' => true,
                'created_by' => $secretary->id,
                'reviewed_by' => $reviewer->id,
                'approved_by' => $admin->id,
                'created_at' => Carbon::now()->subDays(18),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Acta de Sesión Ordinaria N° 002-2024',
                'document_type' => 'acta',
                'content' => 'Segunda sesión ordinaria del año donde se discutieron temas de infraestructura vial y mejoramiento del alumbrado público.',
                'status' => 'review',
                'session_date' => '2024-03-15',
                'is_public' => false,
                'created_by' => $secretary->id,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(5),
            ]
        ];
        
        foreach ($documents as $document) {
            DB::table('documents')->insert($document);
        }
        
        $this->command->info('Documents created successfully!');
        
        // 2. Crear firmas digitales
        $signatures = [
            [
                'id' => Str::uuid(),
                'document_id' => $documents[0]['id'],
                'user_id' => $admin->id,
                'signature_hash' => hash('sha256', $documents[0]['content'] . $admin->id . '2024-01-15'),
                'status' => 'signed',
                'signature_timestamp' => Carbon::now()->subDays(25),
                'signature_data' => 'Digital signature data for admin approval',
                'certificate_info' => 'Certificate: Admin Municipal - Valid until 2025-12-31',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'id' => Str::uuid(),
                'document_id' => $documents[0]['id'],
                'user_id' => $reviewer->id,
                'signature_hash' => hash('sha256', $documents[0]['content'] . $reviewer->id . '2024-01-14'),
                'status' => 'signed',
                'signature_timestamp' => Carbon::now()->subDays(26),
                'signature_data' => 'Digital signature data for reviewer',
                'certificate_info' => 'Certificate: Revisor Legal - Valid until 2025-12-31',
                'created_at' => Carbon::now()->subDays(26),
                'updated_at' => Carbon::now()->subDays(26),
            ],
            [
                'id' => Str::uuid(),
                'document_id' => $documents[1]['id'],
                'user_id' => $reviewer->id,
                'signature_hash' => hash('sha256', $documents[1]['content'] . $reviewer->id . '2024-02-12'),
                'status' => 'signed',
                'signature_timestamp' => Carbon::now()->subDays(16),
                'signature_data' => 'Digital signature data for resolution review',
                'certificate_info' => 'Certificate: Revisor Legal - Valid until 2025-12-31',
                'created_at' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => Str::uuid(),
                'document_id' => $documents[4]['id'],
                'user_id' => $admin->id,
                'signature_hash' => hash('sha256', $documents[4]['content'] . $admin->id . '2024-03-01'),
                'status' => 'signed',
                'signature_timestamp' => Carbon::now()->subDays(12),
                'signature_data' => 'Digital signature data for educational agreement approval',
                'certificate_info' => 'Certificate: Admin Municipal - Valid until 2025-12-31',
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(12),
            ],
            [
                'id' => Str::uuid(),
                'document_id' => $documents[2]['id'],
                'user_id' => $reviewer->id,
                'signature_hash' => hash('sha256', $documents[2]['content'] . $reviewer->id . '2024-03-08'),
                'status' => 'pending',
                'signature_timestamp' => Carbon::now()->subDays(8),
                'signature_data' => null,
                'certificate_info' => 'Certificate: Revisor Legal - Pending signature',
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(8),
            ]
        ];
        
        foreach ($signatures as $signature) {
            DB::table('digital_signatures')->insert($signature);
        }
        
        $this->command->info('Digital signatures created successfully!');
        
        // 3. Crear revisiones de documentos
        $revisions = [
            [
                'id' => Str::uuid(),
                'document_id' => $documents[0]['id'],
                'revision_number' => 1,
                'content' => 'Versión inicial del acta de sesión ordinaria.',
                'changes_summary' => 'Documento inicial creado por el secretario',
                'revised_by' => $secretary->id,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'id' => Str::uuid(),
                'document_id' => $documents[0]['id'],
                'revision_number' => 2,
                'content' => 'Versión revisada con correcciones menores en la redacción.',
                'changes_summary' => 'Correcciones de estilo y gramática',
                'revised_by' => $reviewer->id,
                'created_at' => Carbon::now()->subDays(27),
                'updated_at' => Carbon::now()->subDays(27),
            ],
            [
                'id' => Str::uuid(),
                'document_id' => $documents[1]['id'],
                'revision_number' => 1,
                'content' => 'Versión inicial de la resolución de contratación.',
                'changes_summary' => 'Documento inicial',
                'revised_by' => $secretary->id,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => Str::uuid(),
                'document_id' => $documents[2]['id'],
                'revision_number' => 1,
                'content' => 'Versión inicial de la ordenanza de gestión de residuos.',
                'changes_summary' => 'Documento inicial pendiente de revisión',
                'revised_by' => $secretary->id,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ]
        ];
        
        foreach ($revisions as $revision) {
            DB::table('document_revisions')->insert($revision);
        }
        
        $this->command->info('Document revisions created successfully!');
        
        // 4. Crear logs de auditoría
        $auditLogs = [
            [
                'id' => Str::uuid(),
                'user_id' => $secretary->id,
                'document_id' => $documents[0]['id'],
                'action' => 'created',
                'details' => json_encode(['description' => 'Documento creado: Acta de Sesión Ordinaria N° 001-2024', 'document_type' => 'acta']),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $reviewer->id,
                'document_id' => $documents[0]['id'],
                'action' => 'reviewed',
                'details' => json_encode(['description' => 'Documento revisado y firmado para aprobación', 'status_change' => 'draft->review']),
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(26),
                'updated_at' => Carbon::now()->subDays(26),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $admin->id,
                'document_id' => $documents[0]['id'],
                'action' => 'approved',
                'details' => json_encode(['description' => 'Documento aprobado y publicado', 'status_change' => 'review->published']),
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $secretary->id,
                'document_id' => $documents[1]['id'],
                'action' => 'created',
                'details' => json_encode(['description' => 'Documento creado: Resolución N° 005-2024', 'document_type' => 'resolution']),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $reviewer->id,
                'document_id' => $documents[1]['id'],
                'action' => 'reviewed',
                'details' => json_encode(['description' => 'Documento revisado - Resolución de contratación', 'changes' => 'Minor corrections']),
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(16),
                'updated_at' => Carbon::now()->subDays(16),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $secretary->id,
                'document_id' => $documents[2]['id'],
                'action' => 'created',
                'details' => json_encode(['description' => 'Documento creado: Ordenanza Municipal sobre Gestión de Residuos', 'document_type' => 'ordinance']),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $secretary->id,
                'document_id' => $documents[2]['id'],
                'action' => 'transcribed',
                'details' => json_encode(['description' => 'Audio transcrito automáticamente usando IA', 'ai_service' => 'Whisper', 'confidence' => 0.95]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Python-AI-Service/1.0',
                'created_at' => Carbon::now()->subDays(9),
                'updated_at' => Carbon::now()->subDays(9),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $secretary->id,
                'document_id' => $documents[3]['id'],
                'action' => 'created',
                'details' => json_encode(['description' => 'Documento creado: Acta de Sesión Extraordinaria', 'document_type' => 'acta', 'emergency' => true]),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $admin->id,
                'document_id' => null,
                'action' => 'login',
                'details' => json_encode(['description' => 'Administrador inició sesión en el sistema', 'session_id' => session_id()]),
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $viewer->id,
                'document_id' => $documents[0]['id'],
                'action' => 'viewed',
                'details' => json_encode(['description' => 'Ciudadano consultó documento público', 'public_access' => true]),
                'ip_address' => '201.123.45.67',
                'user_agent' => 'Mozilla/5.0 (Android 11; Mobile) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(1),
                'updated_at' => Carbon::now()->subHours(1),
            ]
        ];
        
        foreach ($auditLogs as $log) {
            DB::table('audit_logs')->insert($log);
        }
        
        $this->command->info('Audit logs created successfully!');
        
        // 5. Estadísticas finales
        $totalUsers = DB::table('users')->count();
        $totalDocs = DB::table('documents')->count();
        $totalSigs = DB::table('digital_signatures')->count();
        $totalLogs = DB::table('audit_logs')->count();
        
        $this->command->info("Sample data creation completed!");
        $this->command->line("📊 Summary:");
        $this->command->line("👥 Total Users: {$totalUsers}");
        $this->command->line("📄 Total Documents: {$totalDocs}");
        $this->command->line("✍️  Total Signatures: {$totalSigs}");
        $this->command->line("📋 Total Audit Logs: {$totalLogs}");
        $this->command->line("");
        $this->command->line("🎯 Documents by status:");
        
        $statusCounts = DB::table('documents')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        foreach ($statusCounts as $status) {
            $this->command->line("   {$status->status}: {$status->count}");
        }
    }
}