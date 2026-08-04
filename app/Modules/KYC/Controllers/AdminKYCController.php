<?php

namespace App\Modules\KYC\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KYC;
use App\Models\KYCDocument;
use App\Modules\KYC\Services\KYCService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminKYCController extends Controller
{
    public function __construct(
        private readonly KYCService $kycService
    ) {}

    /**
     * List all pending or reviewed KYC applications with filter parameters.
     */
    public function index(Request $request): View
    {
        $query = KYC::with(['user.profile', 'documents']);

        // 0. Search Filter (Name, Email, NIK, Phone, Application ID)
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'ilike', "%{$search}%")
                  ->orWhere('id', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'ilike', "%{$search}%")
                         ->orWhereHas('profile', function ($pq) use ($search) {
                             $pq->where('full_name', 'ilike', "%{$search}%")
                               ->orWhere('phone', 'ilike', "%{$search}%");
                         });
                  });
            });
        }

        // 1. Risk Level Filter
        if ($risk = $request->input('risk')) {
            if ($risk === 'high') {
                $query->where('status', 'rejected');
            } elseif ($risk === 'medium') {
                $query->where('status', 'pending');
            } elseif ($risk === 'low') {
                $query->where('status', 'approved');
            }
        }

        // 2. Document Type Filter
        if ($type = $request->input('type')) {
            $query->whereHas('documents', function ($dq) use ($type) {
                if ($type === 'ktp') {
                    $dq->whereIn('type', ['ktp', 'identity_card']);
                } elseif ($type === 'passport') {
                    $dq->where('type', 'passport');
                } elseif ($type === 'sim' || $type === 'driver_license') {
                    $dq->whereIn('type', ['sim', 'driver_license']);
                }
            });
        }

        // 3. Submission Date Filter
        if ($days = (int) $request->input('date')) {
            if (in_array($days, [7, 30, 90, 365])) {
                $query->where('created_at', '>=', now()->subDays($days));
            }
        }

        $kycs = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.kyc.index', compact('kycs'));
    }

    /**
     * Show details of a specific KYC application.
     */
    public function show(KYC $kyc): View
    {
        $kyc->load(['user.profile', 'documents']);
        return view('admin.kyc.show', compact('kyc'));
    }

    /**
     * Approve the KYC request.
     */
    public function approve(KYC $kyc): RedirectResponse
    {
        try {
            $this->kycService->approveKYC($kyc, Auth::user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('admin.kyc.index')
            ->with('success', "KYC application for user {$kyc->user->email} has been approved.");
    }

    /**
     * Reject the KYC request.
     */
    public function reject(Request $request, KYC $kyc): RedirectResponse
    {
        $request->validate([
            'rejected_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        try {
            $this->kycService->rejectKYC($kyc, Auth::user(), $request->rejected_reason);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('admin.kyc.index')
            ->with('success', "KYC application for user {$kyc->user->email} has been rejected.");
    }

    /**
     * Stream private KYC documents securely for authorized admins.
     */
    public function streamDocument(KYCDocument $document): StreamedResponse
    {
        // 1. Safety check
        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found on storage.');
        }

        // 2. Stream private file directly to the browser
        return Storage::disk('local')->response($document->file_path);
    }
}
