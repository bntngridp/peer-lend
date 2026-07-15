<?php

namespace App\Modules\Shared\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    // ─── Notification Type Constants ──────────────────────────────────────────

    const TYPE_KYC_APPROVED           = 'kyc_approved';
    const TYPE_KYC_REJECTED           = 'kyc_rejected';
    const TYPE_LOAN_OPEN_FUNDING      = 'loan_open_funding';
    const TYPE_LOAN_FULLY_FUNDED      = 'loan_fully_funded';
    const TYPE_LOAN_DISBURSED         = 'loan_disbursed';
    const TYPE_INSTALLMENT_DUE        = 'installment_due';
    const TYPE_INSTALLMENT_OVERDUE    = 'installment_overdue';
    const TYPE_INSTALLMENT_PAID       = 'installment_paid';
    const TYPE_LOAN_COMPLETED         = 'loan_completed';
    const TYPE_LOAN_LIQUIDATED        = 'loan_liquidated';
    const TYPE_LTV_WARNING            = 'ltv_warning';

    // ─── Core Method ──────────────────────────────────────────────────────────

    /**
     * Send a notification to a specific user.
     */
    public function send(
        User $user,
        string $type,
        string $title,
        string $body,
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data,
            'read_at' => null,
        ]);
    }

    // ─── KYC Notifications ────────────────────────────────────────────────────

    public function notifyKycApproved(User $user): void
    {
        $this->send(
            $user,
            self::TYPE_KYC_APPROVED,
            'KYC Verification Approved 🎉',
            'Selamat! Identitas kamu telah berhasil diverifikasi. Kamu kini dapat mengajukan pinjaman dan mendanai peminjam di marketplace.',
            ['route' => 'dashboard']
        );
    }

    public function notifyKycRejected(User $user, string $reason): void
    {
        $this->send(
            $user,
            self::TYPE_KYC_REJECTED,
            'KYC Verification Rejected',
            "Maaf, verifikasi identitasmu ditolak. Alasan: {$reason}. Silahkan ajukan ulang dengan dokumen yang valid.",
            ['route' => 'kyc.index']
        );
    }

    // ─── Loan Notifications ───────────────────────────────────────────────────

    /**
     * Notify all active lenders about a new loan open for funding.
     */
    public function notifyLoanOpenFunding(User $borrower, string $loanId, string $amount): void
    {
        // Notify the borrower that their loan is now open
        $this->send(
            $borrower,
            self::TYPE_LOAN_OPEN_FUNDING,
            'Pinjaman Kamu Disetujui! ✅',
            "Pengajuan pinjaman sebesar Rp " . number_format((float)$amount, 0, ',', '.') . " telah disetujui admin dan kini terbuka untuk pendanaan di marketplace.",
            ['loan_id' => $loanId, 'route' => 'loans.index']
        );
    }

    public function notifyLoanFullyFunded(User $borrower, string $loanId): void
    {
        $this->send(
            $borrower,
            self::TYPE_LOAN_FULLY_FUNDED,
            'Pinjaman Kamu 100% Terdanai! 🎊',
            'Selamat! Pinjaman kamu telah sepenuhnya didanai oleh investor. Dana akan segera dicairkan oleh tim kami.',
            ['loan_id' => $loanId, 'route' => 'loans.installments']
        );
    }

    public function notifyLoanDisbursed(User $borrower, string $loanId, string $amount): void
    {
        $this->send(
            $borrower,
            self::TYPE_LOAN_DISBURSED,
            'Dana Pinjaman Telah Dicairkan! 💰',
            "Dana sebesar Rp " . number_format((float)$amount, 0, ',', '.') . " telah masuk ke wallet kamu. Pastikan membayar cicilan tepat waktu.",
            ['loan_id' => $loanId, 'route' => 'wallet.index']
        );
    }

    public function notifyLenderDisbursed(User $lender, string $loanId, string $amount): void
    {
        $this->send(
            $lender,
            self::TYPE_LOAN_DISBURSED,
            'Pinjaman yang Kamu Danai Telah Cair! 📊',
            "Pinjaman yang kamu danai sebesar Rp " . number_format((float)$amount, 0, ',', '.') . " telah dicairkan ke peminjam. Cicilan pertama akan segera ditagihkan.",
            ['loan_id' => $loanId, 'route' => 'marketplace.show']
        );
    }

    // ─── Installment Notifications ────────────────────────────────────────────

    public function notifyInstallmentDue(User $borrower, string $installmentId, string $dueDate, string $amount): void
    {
        $this->send(
            $borrower,
            self::TYPE_INSTALLMENT_DUE,
            'Cicilan Jatuh Tempo dalam 3 Hari ⏰',
            "Cicilan sebesar Rp " . number_format((float)$amount, 0, ',', '.') . " akan jatuh tempo pada {$dueDate}. Pastikan saldo wallet kamu mencukupi.",
            ['installment_id' => $installmentId, 'route' => 'loans.installments']
        );
    }

    public function notifyInstallmentOverdue(User $borrower, string $installmentId, string $totalDue, int $daysOverdue): void
    {
        $this->send(
            $borrower,
            self::TYPE_INSTALLMENT_OVERDUE,
            'Cicilan Telah Menunggak! 🚨',
            "Cicilan kamu telah terlambat selama {$daysOverdue} hari. Total yang harus dibayar saat ini (termasuk denda harian): Rp " . number_format((float)$totalDue, 0, ',', '.') . ". Harap segera lakukan pembayaran.",
            ['installment_id' => $installmentId, 'route' => 'loans.installments']
        );
    }

    public function notifyInstallmentPaid(User $lender, string $loanId, string $principalShare, string $interestShare): void
    {
        $total = bcadd($principalShare, $interestShare, 2);
        $this->send(
            $lender,
            self::TYPE_INSTALLMENT_PAID,
            'Cicilan Diterima! 💸',
            "Peminjam telah membayar cicilan. Kamu menerima Rp " . number_format((float)$total, 0, ',', '.') . " (pokok + bunga) yang sudah masuk ke wallet kamu.",
            ['loan_id' => $loanId, 'route' => 'wallet.index']
        );
    }

    public function notifyLoanCompleted(User $user, string $loanId): void
    {
        $this->send(
            $user,
            self::TYPE_LOAN_COMPLETED,
            'Pinjaman Selesai Lunas! 🏆',
            'Semua cicilan telah dibayar. Terima kasih telah menggunakan Peer-Lend!',
            ['loan_id' => $loanId]
        );
    }

    // ─── Liquidation Notifications ────────────────────────────────────────────

    public function notifyLtvWarning(User $borrower, string $loanId, string $currentLtv): void
    {
        $this->send(
            $borrower,
            self::TYPE_LTV_WARNING,
            'Peringatan: LTV Mendekati Batas Likuidasi! ⚠️',
            "Nilai jaminan kamu sedang turun. LTV saat ini: {$currentLtv}% (batas likuidasi: 80%). Segera tambah jaminan atau lunasi pinjaman untuk menghindari likuidasi otomatis.",
            ['loan_id' => $loanId, 'route' => 'loans.index']
        );
    }

    public function notifyBorrowerLiquidated(User $borrower, string $loanId): void
    {
        $this->send(
            $borrower,
            self::TYPE_LOAN_LIQUIDATED,
            'Jaminan Kamu Telah Dilikuidasi 🔴',
            'LTV pinjaman kamu telah melampaui batas 80%. Jaminan crypto kamu telah dilikuidasi secara otomatis untuk melunasi sebagian utang kepada investor.',
            ['loan_id' => $loanId, 'route' => 'loans.index']
        );
    }

    public function notifyLenderLiquidated(User $lender, string $loanId, string $recovered): void
    {
        $this->send(
            $lender,
            self::TYPE_LOAN_LIQUIDATED,
            'Pinjaman yang Kamu Danai Dilikuidasi 🔴',
            "Pinjaman telah dilikuidasi karena LTV melampaui batas. Kamu menerima Rp " . number_format((float)$recovered, 0, ',', '.') . " dari likuidasi jaminan.",
            ['loan_id' => $loanId, 'route' => 'wallet.index']
        );
    }
}
