<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\GeneratedDocument;
use App\Traits\SendsDatabaseNotifications;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentGeneratorController extends Controller
{
    use SendsDatabaseNotifications;

    /**
     * Show preview/edit form before generation
     */
    public function prepare($requestId, $documentTypeId)
    {
        $documentRequest = DocumentRequest::with(['user', 'items.documentType'])
            ->findOrFail($requestId);
        
        $documentType = DocumentType::findOrFail($documentTypeId);
        
        $requestedItem = $documentRequest->items->firstWhere('document_type_id', $documentTypeId);
        if (!$requestedItem) {
            return back()->with('error', 'This document was not requested.');
        }
        
        $data = $this->prepareDocumentData($documentRequest, $documentType, $requestedItem);
        
        return view('registrar.documents.prepare', compact('documentRequest', 'documentType', 'data', 'requestId', 'documentTypeId'));
    }

    /**
     * Generate a single document
     * GET/POST /registrar/requests/{id}/generate/{document_type_id}
     */
    public function generate(Request $request, $requestId, $documentTypeId)
    {
        $documentRequest = DocumentRequest::with(['user', 'items.documentType'])
            ->findOrFail($requestId);
        
        $documentType = DocumentType::findOrFail($documentTypeId);
        
        // Check if this document type was requested
        $requestedItem = $documentRequest->items->firstWhere('document_type_id', $documentTypeId);
        if (!$requestedItem) {
            return back()->with('error', 'This document was not requested by the student.');
        }
        
        // Get custom data from form if it exists
        $customData = $request->except(['_token']);

        // 1. CHECK FOR .DOCX TEMPLATE
        if ($documentType->template_path && Storage::exists($documentType->template_path)) {
            try {
                $service = new \App\Services\DocumentGeneratorService();
                $filePath = $service->generateFromTemplate($documentRequest, $documentType, $requestedItem, $customData);
                
                // Record the generation
                GeneratedDocument::create([
                    'document_request_id' => $documentRequest->id,
                    'document_type_id' => $documentType->id,
                    'file_path' => $filePath,
                    'printed_at' => now(),
                    'printed_by' => Auth::id(),
                ]);

                // Notify
                $message = "✅ Document '{$documentType->name}' for request {$documentRequest->reference_number} has been generated (.docx).";
                $this->sendNotificationToCurrentUser($message, route('registrar.requests.show', $documentRequest->id));
                session()->flash('check_notifications', true);

                return response()->download(Storage::path($filePath));
            } catch (\Exception $e) {
                return back()->with('error', 'Error generating .docx: ' . $e->getMessage());
            }
        }

        // 2. FALLBACK TO PDF
        // Prepare data for template
        $data = $this->prepareDocumentData($documentRequest, $documentType, $requestedItem);
        
        // Load the appropriate template
        $pdf = $this->loadTemplate($documentType->code, $data);
        
        // Store the generated document record
        $generatedDocument = GeneratedDocument::create([
            'document_request_id' => $documentRequest->id,
            'document_type_id' => $documentType->id,
            'printed_by' => Auth::id(),
        ]);
        
        // Save PDF to storage
        $fileName = $documentRequest->reference_number . '_' . $documentType->code . '_' . time() . '.pdf';
        $filePath = 'generated_documents/' . $fileName;
        Storage::disk('local')->put($filePath, $pdf->output());
        
        $generatedDocument->update([
            'file_path' => $filePath,
            'printed_at' => now(),
        ]);
        
        // Send notification to registrar
        $message = "✅ Document '{$documentType->name}' for request {$documentRequest->reference_number} has been generated (PDF).";
        $this->sendNotificationToCurrentUser($message, route('registrar.requests.show', $documentRequest->id));
        session()->flash('check_notifications', true);
        
        // Return PDF for download/print
        return $pdf->stream($fileName);
    }
    
    /**
     * Print selected documents for a request
     * POST /registrar/documents/print-selected
     */
    public function printSelected(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:document_requests,id',
            'document_item_ids' => 'required|array',
            'document_item_ids.*' => 'exists:document_request_items,id',
        ]);

        $documentRequest = DocumentRequest::with(['items.documentType', 'user'])
            ->findOrFail($validated['request_id']);
        
        $itemIds = $validated['document_item_ids'];
        $generatedFiles = [];
        
        foreach ($documentRequest->items as $item) {
            if (!in_array($item->id, $itemIds)) {
                continue; // Skip if not selected
            }
            
            if (!$item->documentType->is_printable) {
                continue; // Skip non-printable documents
            }
            
            $data = $this->prepareDocumentData($documentRequest, $item->documentType, $item);
            $pdf = $this->loadTemplate($item->documentType->code, $data);
            
            $fileName = $documentRequest->reference_number . '_' . $item->documentType->code . '_' . time() . '.pdf';
            $filePath = 'generated_documents/' . $fileName;
            Storage::disk('local')->put($filePath, $pdf->output());
            
            $generatedFiles[] = [
                'full_path' => storage_path('app/private/' . $filePath),
                'relative_path' => $filePath,
                'name' => $fileName
            ];
            
            // Mark as printed
            GeneratedDocument::updateOrCreate(
                [
                    'document_request_id' => $documentRequest->id,
                    'document_type_id' => $item->document_type_id,
                ],
                [
                    'file_path' => $filePath,
                    'printed_at' => now(),
                    'printed_by' => Auth::id(),
                ]
            );
        }
        
        if (empty($generatedFiles)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No printable documents were generated.'], 400);
            }
            return back()->with('error', 'No printable documents were generated.');
        }

        // Check if all printable documents are now printed
        $printableItemsCount = $documentRequest->items->filter(function($item) {
            return $item->documentType->is_printable;
        })->count();

        $printedItemsCount = GeneratedDocument::where('document_request_id', $documentRequest->id)
            ->whereIn('document_type_id', $documentRequest->items->pluck('document_type_id'))
            ->count();

        if ($printableItemsCount > 0 && $printedItemsCount >= $printableItemsCount) {
            $oldStatus = $documentRequest->status;
            $documentRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Log status change
            \App\Models\StatusLog::create([
                'document_request_id' => $documentRequest->id,
                'changed_by' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => 'completed',
                'notes' => 'All documents printed. Request automatically marked as completed.',
            ]);

            // Notify student
            $student = $documentRequest->user;
            if ($student) {
                $message = "✅ Your documents for request {$documentRequest->reference_number} are now complete and ready. Thank you!";
                $this->sendNotification($student, $message, route('student.requests.history'));
            }
        }
        
        $downloadUrl = '';
        
        // Create ZIP file if multiple documents
        if (count($generatedFiles) > 1) {
            $zipFileName = $documentRequest->reference_number . '_documents_' . time() . '.zip';
            $zipPathRelative = 'generated_documents/' . $zipFileName;
            $zipPathFull = storage_path('app/private/' . $zipPathRelative);
            
            $zip = new ZipArchive();
            if ($zip->open($zipPathFull, ZipArchive::CREATE) === TRUE) {
                foreach ($generatedFiles as $file) {
                    $zip->addFile($file['full_path'], $file['name']);
                }
                $zip->close();
            }
            // For private storage, we need a special route to download
            $downloadUrl = route('registrar.documents.download', ['path' => $zipPathRelative]);
        } else {
            $downloadUrl = route('registrar.documents.download', ['path' => $generatedFiles[0]['relative_path']]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Documents printed successfully.',
                'download_url' => $downloadUrl
            ]);
        }
        
        return redirect()->away($downloadUrl);
    }
    
    /**
     * Preview document before printing
     * GET /registrar/requests/{id}/preview/{document_type_id}
     */
    public function preview($requestId, $documentTypeId)
    {
        try {
            $documentRequest = DocumentRequest::with(['user', 'items.documentType'])
                ->findOrFail($requestId);
            
            $documentType = DocumentType::findOrFail($documentTypeId);
            
            $requestedItem = $documentRequest->items->firstWhere('document_type_id', $documentTypeId);
            if (!$requestedItem) {
                return response()->json(['error' => 'This document was not requested.'], 404);
            }
            
            $data = $this->prepareDocumentData($documentRequest, $documentType, $requestedItem);
            $pdf = $this->loadTemplate($documentType->code, $data);
            
            return $pdf->stream($documentType->code . '_preview.pdf');
        } catch (\Exception $e) {
            \Log::error('PDF Preview Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate preview: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Download generated document
     */
    public function download(Request $request)
    {
        $path = $request->query('path');
        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404);
        }
        
        return response()->download(storage_path('app/private/' . $path));
    }
    
    /**
     * Prepare data for document templates
     */
    private function prepareDocumentData($documentRequest, $documentType, $requestedItem)
    {
        $user = $documentRequest->user;
        
        // Format date
        $currentDate = now()->format('F d, Y');
        
        // Get grades if applicable (placeholder - will be implemented with grade management)
        $grades = $this->getStudentGrades($user);
        
        // Get school year
        $schoolYear = $this->getCurrentSchoolYear();
        
        return [
            // Student Information
            'student_name' => $documentRequest->full_name,
            'student_number' => $documentRequest->student_number,
            'strand' => $documentRequest->course_program,
            'grade_level' => $documentRequest->year_level,
            'section' => $documentRequest->section,
            'contact_number' => $documentRequest->contact_number,
            'address' => $documentRequest->address,
            
            // Document-specific
            'document_name' => $documentType->name,
            'document_code' => $documentType->code,
            'copies' => $requestedItem->copies,
            'assessment_year' => $requestedItem->assessment_year ?? 'N/A',
            'semester' => $requestedItem->semester ?? 'N/A',
            'purpose' => $requestedItem->purpose ?? 'For school requirements',
            
            // Request Information
            'reference_number' => $documentRequest->reference_number,
            'request_date' => $documentRequest->created_at->format('F d, Y'),
            'total_fee' => number_format($documentRequest->total_fee, 2),
            
            // System Information
            'current_date' => $currentDate,
            'school_year' => $schoolYear,
            'registrar_name' => Auth::user()->name,
            'registrar_signature' => 'Registrar\'s Signature',
            
            // Grades (placeholder)
            'grades' => $grades,
            'general_average' => $grades['average'] ?? 'N/A',
            
            // Footer
            'footer_text' => 'This document is issued by the CCST Registrar Office for official purposes only.',
        ];
    }
    
    /**
     * Load the appropriate PDF template
     */
    private function loadTemplate($documentCode, $data)
    {
        switch (strtoupper($documentCode)) {
            case 'COE':
                return Pdf::loadView('pdf.coe-template', $data);
            case 'TC':
                return Pdf::loadView('pdf.tc-template', $data);
            case 'GMC':
            case 'CGMC':
                return Pdf::loadView('pdf.certificate-of-good-moral', $data);
            case 'REG':
                return Pdf::loadView('pdf.registration-form', $data);
            case 'COG':
                return Pdf::loadView('pdf.cog-template', $data);
            case 'CCOMP':
                return Pdf::loadView('pdf.certificate-of-completion', $data);
            case 'CGRAD':
                return Pdf::loadView('pdf.certificate-of-graduation', $data);
            case 'CRANK':
                return Pdf::loadView('pdf.certificate-of-ranking', $data);
            case 'CLID':
                return Pdf::loadView('pdf.certificate-of-lost-id', $data);
            case 'CGWA':
                return Pdf::loadView('pdf.certificate-of-gwa', $data);
            case 'F138':
                return Pdf::loadView('pdf.f138-template', $data);
            case 'TOR':
                return Pdf::loadView('pdf.tor-template', $data);
            default:
                return Pdf::loadView('pdf.generic-template', $data);
        }
    }
    
    /**
     * Get student grades (placeholder)
     */
    private function getStudentGrades($user)
    {
        if (!$user) {
            return [
                'subjects' => [],
                'average' => 'N/A'
            ];
        }
        // TODO: Implement grade management system
        return [
            'subjects' => [
                ['name' => 'English', 'grade' => '90', 'remarks' => 'Passed'],
                ['name' => 'Mathematics', 'grade' => '85', 'remarks' => 'Passed'],
                ['name' => 'Science', 'grade' => '88', 'remarks' => 'Passed'],
                ['name' => 'Filipino', 'grade' => '92', 'remarks' => 'Passed'],
                ['name' => 'Social Studies', 'grade' => '87', 'remarks' => 'Passed'],
            ],
            'average' => '88.4',
        ];
    }
    
    /**
     * Get current school year
     */
    private function getCurrentSchoolYear()
    {
        $year = date('Y');
        $nextYear = $year + 1;
        return "SY {$year}-{$nextYear}";
    }
}