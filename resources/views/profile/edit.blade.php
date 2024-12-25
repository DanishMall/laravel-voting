@extends('layouts.app')

@section('title')
    <title>Profile Management - Student Election System</title>
@endsection

@section('style')
    <style>
        .profile-card {
            transition: all 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .verification-alert {
            background: linear-gradient(135deg, #fecaca 0%, #f87171 100%);
        }
    </style>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Role & Verification Status Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">
                                Welcome, {{ Auth::user()->name }}!
                            </h1>
                            <p class="mt-1 text-gray-600">
                                Role: {{ ucfirst(Auth::user()->role ?? 'voter') }}
                            </p>
                        </div>
                        @if(Auth::user()->role === 'voter' && !Auth::user()->hasVerifiedEmail())
                            <div class="bg-red-50 text-red-700 px-4 py-2 rounded-md">
                                Email Not Verified
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Email Verification Alert -->
            @if(Auth::user()->role === 'voter' && !Auth::user()->hasVerifiedEmail())
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Attention Required</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Please verify your email address to participate in voting.
                                    Check your inbox for the verification link or request a new one below.</p>
                            </div>
                            <div class="mt-4">
                                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        Resend Verification Email
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">A new verification link has been sent to your email address.</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Profile Information Section -->
            <div class="bg-white shadow-sm sm:rounded-lg profile-card mb-6">
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Password Update Section -->
            <div class="bg-white shadow-sm sm:rounded-lg profile-card mb-6">
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Account Deletion Section -->
            <div class="bg-white shadow-sm sm:rounded-lg profile-card">
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('[role="alert"]').forEach(alert => {
                    if (!alert.classList.contains('verification-required')) {
                        alert.remove();
                    }
                });
            }, 5000);

            // Handle verification email sending
            const verificationForm = document.getElementById('send-verification');
            if (verificationForm) {
                verificationForm.addEventListener('submit', function(e) {
                    const button = this.querySelector('button');
                    button.disabled = true;
                    button.textContent = 'Sending...';
                });
            }
        });
    </script>
@endsection
