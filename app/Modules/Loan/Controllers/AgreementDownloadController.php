<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AgreementDownloadController extends Controller
{
    /**
     * Show the printable/downloadable Contract Agreement page.
     * Accessible only by Borrower, Lender of this loan, or Admin.
     */
    public function download(LoanRequest $loan): View
    {
        $user = Auth::user();

        // Security Authorization Check
        $hasFunded = $loan->fundings()->where('lender_id', $user->id)->exists();
        if (!$user->isAdmin() && $loan->borrower_id !== $user->id && !$hasFunded) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat dokumen perjanjian ini.');
        }

        if ($loan->status === LoanRequest::STATUS_PENDING) {
            abort(400, 'Perjanjian Kontrak belum tersedia sebelum pinjaman disetujui.');
        }

        // Get active lenders for details
        $funders = $loan->fundings()->with('lender.profile')->get();

        return view('loans.agreement', compact('loan', 'funders'));
    }
}
