<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\Appointment;
use App\Models\User;
use App\Traits\SendsDatabaseNotifications;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    use SendsDatabaseNotifications;

    /**
     * Display reports page
     */
    public function index()
    {
        // Get data for dashboard charts
        $monthlyRequests = $this->getMonthlyRequests();
        $monthlyAppointments = $this->getMonthlyAppointments();
        $topDocuments = $this->getTopDocuments();
        $peakHours = $this->getPeakHours();
        $statusDistribution = $this->getStatusDistribution();

        return view('registrar.reports.index', compact(
            'monthlyRequests',
            'monthlyAppointments',
            'topDocuments',
            'peakHours',
            'statusDistribution'
        ));
    }

    /**
     * Export report as PDF
     */
    public function export(Request $request)
    {
        $reportType = $request->input('report_type');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $includeSummary = $request->has('include_summary');

        switch ($reportType) {
            case 'requests':
                return $this->exportRequestsReport($dateFrom, $dateTo, $includeSummary);
            case 'payments':
                return $this->exportPaymentsReport($dateFrom, $dateTo, $includeSummary);
            case 'appointments':
                return $this->exportAppointmentsReport($dateFrom, $dateTo, $includeSummary);
            case 'students':
                return $this->exportStudentsReport($dateFrom, $dateTo, $includeSummary);
            default:
                return back()->with('error', 'Invalid report type.');
        }
    }

    /**
     * Export Document Requests Report
     */
    private function exportRequestsReport($dateFrom, $dateTo, $includeSummary)
    {
        $requests = DocumentRequest::with(['user', 'items.documentType'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAmount = $requests->sum('total_fee');
        $statusCounts = $requests->groupBy('status')->map->count();

        $data = [
            'title' => 'Document Requests Report',
            'subtitle' => Carbon::parse($dateFrom)->format('F d, Y') . ' - ' . Carbon::parse($dateTo)->format('F d, Y'),
            'requests' => $requests,
            'totalRequests' => $requests->count(),
            'totalAmount' => $totalAmount,
            'statusCounts' => $statusCounts,
            'includeSummary' => $includeSummary,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('pdf.requests-report', $data);
        return $pdf->download('requests-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Payments Report
     */
    private function exportPaymentsReport($dateFrom, $dateTo, $includeSummary)
    {
        $payments = DocumentRequest::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCollected = $payments->where('payment_status', 'paid')->sum('total_fee');
        $statusCounts = $payments->groupBy('payment_status')->map->count();

        $data = [
            'title' => 'Payments Report',
            'subtitle' => Carbon::parse($dateFrom)->format('F d, Y') . ' - ' . Carbon::parse($dateTo)->format('F d, Y'),
            'payments' => $payments,
            'totalPayments' => $payments->count(),
            'totalCollected' => $totalCollected,
            'statusCounts' => $statusCounts,
            'includeSummary' => $includeSummary,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('pdf.payments-report', $data);
        return $pdf->download('payments-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Appointments Report
     */
    private function exportAppointmentsReport($dateFrom, $dateTo, $includeSummary)
    {
        $appointments = Appointment::with(['student', 'timeSlot'])
            ->whereBetween('appointment_date', [$dateFrom, $dateTo])
            ->orderBy('appointment_date', 'asc')
            ->get();

        $statusCounts = $appointments->groupBy('status')->map->count();
        $totalApps = $appointments->count();
        $completedApps = $appointments->where('status', 'completed')->count();
        $missedApps = $appointments->where('status', 'missed')->count();
        
        $attendanceRate = $totalApps > 0 
            ? round(($completedApps / $totalApps) * 100, 2)
            : 0;

        $data = [
            'title' => 'Appointments Report',
            'subtitle' => Carbon::parse($dateFrom)->format('F d, Y') . ' - ' . Carbon::parse($dateTo)->format('F d, Y'),
            'appointments' => $appointments,
            'totalAppointments' => $totalApps,
            'statusCounts' => $statusCounts,
            'completedApps' => $completedApps,
            'missedApps' => $missedApps,
            'attendanceRate' => $attendanceRate,
            'includeSummary' => $includeSummary,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('pdf.appointments-report', $data);
        return $pdf->download('appointments-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Students Report
     */
    private function exportStudentsReport($dateFrom, $dateTo, $includeSummary)
    {
        $students = User::where('role', 'student')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('last_name')
            ->get();

        $gradeCounts = $students->groupBy('grade_level')->map->count();
        $strandCounts = $students->groupBy('strand')->map->count();

        $data = [
            'title' => 'Students Report',
            'subtitle' => Carbon::parse($dateFrom)->format('F d, Y') . ' - ' . Carbon::parse($dateTo)->format('F d, Y'),
            'students' => $students,
            'totalStudents' => $students->count(),
            'gradeCounts' => $gradeCounts,
            'strandCounts' => $strandCounts,
            'includeSummary' => $includeSummary,
            'generated_at' => now()->format('F d, Y h:i A'),
            'generated_by' => auth()->user()->name,
        ];

        $pdf = Pdf::loadView('pdf.students-report', $data);
        return $pdf->download('students-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Get monthly request data for chart
     */
    private function getMonthlyRequests()
    {
        $labels = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = DocumentRequest::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get monthly appointment data for chart
     */
    private function getMonthlyAppointments()
    {
        $labels = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[] = Appointment::whereYear('appointment_date', $month->year)
                ->whereMonth('appointment_date', $month->month)
                ->count();
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get top requested documents
     */
    private function getTopDocuments()
    {
        return \DB::table('document_request_items')
            ->join('document_types', 'document_request_items.document_type_id', '=', 'document_types.id')
            ->select('document_types.name', \DB::raw('count(*) as count'))
            ->groupBy('document_types.id', 'document_types.name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get peak appointment hours
     */
    private function getPeakHours()
    {
        $hours = [];
        for ($i = 8; $i <= 16; $i++) {
            $hour = $i;
            $displayHour = $hour > 12 ? ($hour - 12) . ' PM' : ($hour == 12 ? '12 PM' : $hour . ' AM');
            $hours[] = $displayHour;
        }
        
        return $hours;
    }

    /**
     * Get status distribution for pie chart
     */
    private function getStatusDistribution()
    {
        return [
            'pending' => DocumentRequest::where('status', 'pending')
                ->whereDoesntHave('appointment', function($q) {
                    $q->where('status', 'missed');
                })->count(),
            'missed' => DocumentRequest::where('status', 'pending')
                ->whereHas('appointment', function($q) {
                    $q->where('status', 'missed');
                })->count(),
            'ready_for_pickup' => DocumentRequest::where('status', 'ready_for_pickup')->count(),
            'completed' => DocumentRequest::where('status', 'completed')->count(),
            'cancelled' => DocumentRequest::where('status', 'cancelled')->count(),
        ];
    }
}