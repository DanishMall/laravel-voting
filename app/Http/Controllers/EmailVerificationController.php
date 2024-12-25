<?php
// EmailVerificationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class EmailVerificationController extends Controller
{
    public function notice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('profile.edit')
            : view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('profile.edit');
        }

        $request->fulfill();

        return redirect()->route('profile.edit')
            ->with('status', 'email-verified')
            ->with('message', 'Your email has been verified successfully!');
    }

    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('profile.edit');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()
            ->with('status', 'verification-link-sent')
            ->with('message', 'A new verification link has been sent to your email address.');
    }
}
