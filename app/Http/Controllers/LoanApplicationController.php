<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Enums\LoanType;
use App\Http\Requests\StoreLoanApplicationRequest;
use App\Http\Requests\UpdateLoanStatusRequest;
use App\Models\LoanApplication;
use App\Services\LoanCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanApplicationController extends Controller
{
    public function __construct(
        protected LoanCalculationService $calculator
    ) {}

    /**
     * Display a listing of loan applications with statistical overview.
     */
    public function index(Request $request): View
    {
        $searchTerm = $request->string('search')->toString();
        $statusFilter = $request->string('status')->toString();
        $typeFilter = $request->string('type')->toString();

        $query = LoanApplication::query()
            ->search($searchTerm)
            ->status($statusFilter)
            ->type($typeFilter)
            ->latest();

        $loans = $query->paginate(10)->withQueryString();

        // Statistical KPI metrics
        $stats = [
            'total' => LoanApplication::count(),
            'pending' => LoanApplication::where('status', LoanStatus::PENDING)->count(),
            'approved' => LoanApplication::where('status', LoanStatus::APPROVED)->count(),
            'rejected' => LoanApplication::where('status', LoanStatus::REJECTED)->count(),
            'total_approved_amount' => LoanApplication::where('status', LoanStatus::APPROVED)->sum('loan_amount'),
        ];

        return view('loans.index', [
            'loans' => $loans,
            'stats' => $stats,
            'loanTypes' => LoanType::cases(),
            'loanStatuses' => LoanStatus::cases(),
            'currentSearch' => $searchTerm,
            'currentStatus' => $statusFilter,
            'currentType' => $typeFilter,
        ]);
    }

    /**
     * Store a newly created loan application in storage.
     */
    public function store(StoreLoanApplicationRequest $request): RedirectResponse|JsonResponse
    {
        if (auth()->check() && auth()->user()->isAnalyst()) {
            abort(403, 'Akses ditolak. Credit Analyst tidak diizinkan mencatat pengajuan baru.');
        }

        $validated = $request->validated();

        $calculation = $this->calculator->calculate(
            (float) $validated['loan_amount'],
            (int) $validated['tenor_months']
        );

        $loan = LoanApplication::create([
            'customer_name' => $validated['customer_name'],
            'loan_type' => $validated['loan_type'],
            'loan_amount' => $validated['loan_amount'],
            'tenor_months' => $validated['tenor_months'],
            'monthly_income' => $validated['monthly_income'],
            'monthly_installment' => $calculation['monthly_installment'],
            'interest_rate_monthly' => $calculation['interest_rate_monthly'],
            'status' => LoanStatus::PENDING,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pembiayaan untuk nasabah ' . $loan->customer_name . ' berhasil dicatat.',
                'data' => $loan,
            ], 201);
        }

        return redirect()->route('loans.index')
            ->with('success', 'Pengajuan pembiayaan untuk nasabah ' . $loan->customer_name . ' berhasil dicatat.');
    }

    /**
     * Display the specified loan application with detailed installment calculation.
     */
    public function show(LoanApplication $loan): JsonResponse|View
    {
        $calculation = $this->calculator->calculate(
            (float) $loan->loan_amount,
            (int) $loan->tenor_months,
            (float) $loan->interest_rate_monthly
        );

        $schedule = $this->calculator->generateSchedule(
            (float) $loan->loan_amount,
            (int) $loan->tenor_months,
            (float) $loan->interest_rate_monthly
        );

        $data = [
            'loan' => $loan,
            'customer_name' => $loan->customer_name,
            'loan_type_label' => $loan->loan_type->label(),
            'loan_type_value' => $loan->loan_type->value,
            'status_label' => $loan->status->label(),
            'status_value' => $loan->status->value,
            'status_badge' => $loan->status->badgeClasses(),
            'loan_amount' => (float) $loan->loan_amount,
            'formatted_loan_amount' => $loan->formatted_loan_amount,
            'tenor_months' => $loan->tenor_months,
            'monthly_income' => (float) $loan->monthly_income,
            'formatted_monthly_income' => $loan->formatted_monthly_income,
            'monthly_installment' => (float) $loan->monthly_installment,
            'formatted_monthly_installment' => $loan->formatted_monthly_installment,
            'debt_to_income_ratio' => $loan->debt_to_income_ratio,
            'notes' => $loan->notes,
            'rejection_reason' => $loan->rejection_reason,
            'created_at' => $loan->formatted_created_at,
            'actioned_at' => $loan->actioned_at ? $loan->actioned_at->translatedFormat('d M Y, H:i') : null,
            'actioned_by' => $loan->actioned_by,
            'calculation' => $calculation,
            'schedule' => $schedule,
        ];

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($data);
        }

        return view('loans.show', $data);
    }

    /**
     * Approve the loan application.
     */
    public function approve(LoanApplication $loan): RedirectResponse|JsonResponse
    {
        if (! $loan->status->isPending()) {
            $msg = 'Pengajuan ini sudah pernah diproses sebelumnya (' . $loan->status->label() . ').';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $loan->approve('Internal Staff');

        $successMsg = 'Pengajuan pembiayaan atas nama ' . $loan->customer_name . ' sebesar ' . $loan->formatted_loan_amount . ' telah berhasil DISETUJUI.';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'status' => $loan->status->value,
            ]);
        }

        return redirect()->route('loans.index')->with('success', $successMsg);
    }

    /**
     * Reject the loan application with reason.
     */
    public function reject(UpdateLoanStatusRequest $request, LoanApplication $loan): RedirectResponse|JsonResponse
    {
        if (! $loan->status->isPending()) {
            $msg = 'Pengajuan ini sudah pernah diproses sebelumnya (' . $loan->status->label() . ').';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $reason = $request->input('rejection_reason') ?: 'Tidak memenuhi kualifikasi persetujuan kredit internal';
        $loan->reject($reason, 'Internal Staff');

        $rejectedMsg = 'Pengajuan pembiayaan atas nama ' . $loan->customer_name . ' telah DITOLAK.';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $rejectedMsg,
                'status' => $loan->status->value,
            ]);
        }

        return redirect()->route('loans.index')->with('success', $rejectedMsg);
    }

    /**
     * Live preview calculation API endpoint for real-time form simulation.
     */
    public function calculatePreview(Request $request): JsonResponse
    {
        $amount = (float) preg_replace('/[^\d]/', '', (string) $request->input('amount', 0));
        $tenor = (int) $request->input('tenor', 12);

        if ($amount <= 0 || $tenor <= 0) {
            return response()->json([
                'valid' => false,
                'monthly_installment' => 0,
                'formatted_monthly_installment' => 'Rp 0',
            ]);
        }

        $calculation = $this->calculator->calculate($amount, $tenor);

        return response()->json([
            'valid' => true,
            'calculation' => $calculation,
        ]);
    }
}
