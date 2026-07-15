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
     * List all pending or reviewed KYC applications.
     */
    public function index(): View
    {
        $kycs = KYC::with(['user.profile'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

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
