<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanMessage;
use App\Models\LoanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanMessageController extends Controller
{
    /**
     * Authorize if user is participant of the loan request.
     */
    private function authorizeLoanParticipant(LoanRequest $loan): void
    {
        $user = Auth::user();

        // Admin can access all chats
        if ($user->isAdmin()) {
            return;
        }

        // Borrower can access their own loan chats
        if ($loan->borrower_id === $user->id) {
            return;
        }

        // Lender can access if they have funded this loan
        $hasFunded = $loan->fundings()->where('lender_id', $user->id)->exists();
        if ($hasFunded) {
            return;
        }

        abort(403, 'Anda tidak diizinkan mengakses chat untuk pinjaman ini.');
    }

    /**
     * Fetch all messages for a specific loan (JSON).
     */
    public function fetchMessages(LoanRequest $loan): JsonResponse
    {
        $this->authorizeLoanParticipant($loan);

        $messages = LoanMessage::with('sender.profile')
            ->where('loan_request_id', $loan->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'         => $msg->id,
                    'message'    => e($msg->message),
                    'sender_id'  => $msg->sender_id,
                    'sender_name'=> $msg->sender->profile->full_name ?? $msg->sender->email,
                    'is_me'      => $msg->sender_id === Auth::id(),
                    'time'       => $msg->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'success'  => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a new message to a specific loan chat (AJAX).
     */
    public function sendMessage(Request $request, LoanRequest $loan): JsonResponse
    {
        $this->authorizeLoanParticipant($loan);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = LoanMessage::create([
            'loan_request_id' => $loan->id,
            'sender_id'       => Auth::id(),
            'message'         => $validated['message'],
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id'         => $message->id,
                'message'    => e($message->message),
                'sender_id'  => $message->sender_id,
                'sender_name'=> Auth::user()->profile->full_name ?? Auth::user()->email,
                'is_me'      => true,
                'time'       => $message->created_at->format('H:i'),
            ]
        ]);
    }
}
