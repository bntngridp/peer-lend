<?php

namespace App\Modules\KYC\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\KYC\Requests\SubmitKYCRequest;
use App\Modules\KYC\Services\KYCService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KYCController extends Controller
{
    public function __construct(
        private readonly KYCService $kycService
    ) {}

    /**
     * Display the user's current KYC status or submission form.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // 1. Force profile completion first
        if (! $user->profile || ! $user->profile->phone) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Please complete your profile and phone number details before submitting KYC.');
        }

        $kyc = $user->kyc;

        return view('kyc.index', compact('user', 'kyc'));
    }

    /**
     * Process the KYC files upload.
     */
    public function submit(SubmitKYCRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $this->kycService->submitKYC($user, $request->validated());

        return redirect()->route('kyc.index')
            ->with('success', 'Your KYC documents have been uploaded successfully and are awaiting review.');
    }
}
