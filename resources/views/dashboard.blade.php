@extends('layouts.app')

@section('meta')
    <meta name="description" content="Student Election Voting System - Cast your vote securely">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title')
    <title>Student Election Dashboard</title>
@endsection

@include('layouts.navigation')

@section('style')
    <style>
        .candidate-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .candidate-card.selected {
            border-color: #4f46e5;
            background-color: #f5f3ff;
        }
        .stats-card {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }
        .vote-button {
            transition: all 0.2s ease;
        }
        .vote-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .vote-confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
        }
        .loading-spinner {
            display: none;
            width: 1.5rem;
            height: 1.5rem;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Voting Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Voters</h3>
                    <p class="text-2xl font-semibold text-indigo-600">{{ number_format($votingStatus['total_voters']) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900">Votes Cast</h3>
                    <p class="text-2xl font-semibold text-indigo-600">{{ number_format($votingStatus['total_votes_cast']) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900">Voter Turnout</h3>
                    <p class="text-2xl font-semibold text-indigo-600">{{ $votingStatus['voting_percentage'] }}%</p>
                </div>
            </div>

            <!-- User Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">
                                Welcome, {{ Auth::user()->name }}!
                            </h1>
                            <p class="mt-1 text-gray-600">
                                @if(!Auth::user()->has_voted && $canVote)
                                    You haven't cast your vote yet. Choose your candidates wisely!
                                @elseif(!$canVote)
                                    Please verify your email to participate in voting.
                                @else
                                    Thank you for participating in the election!
                                @endif
                            </p>
                        </div>
                        @if(Auth::user()->has_voted)
                            <a href="{{ route('voting.history') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition-colors">
                                View Voting History
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6" role="alert" id="success-alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" class="close-alert">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6" role="alert" id="error-alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" class="close-alert">
                                <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Candidates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($candidates as $candidate)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg candidate-card"
                         data-candidate-id="{{ $candidate->id }}">
                        <div class="p-6">
                            <div class="relative">
                                @if($candidate->image_path)
                                    <img src="{{ asset('storage/' . $candidate->image_path) }}"
                                         alt="{{ $candidate->name }}"
                                         class="w-full h-48 object-cover rounded-lg mb-4">
                                @else
                                    <div class="w-full h-48 bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                                        <svg class="h-20 w-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif

                                @if($candidate->campaign_end_date < now())
                                    <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-md text-xs">
                                        Campaign Ended
                                    </div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $candidate->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $candidate->position }} - {{ $candidate->faculty }}</p>
                            </div>

                            <div class="space-y-3">
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <h4 class="text-sm font-medium text-gray-900">Vision</h4>
                                    <p class="text-sm text-gray-600">{{ $candidate->vision }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <h4 class="text-sm font-medium text-gray-900">Mission</h4>
                                    <p class="text-sm text-gray-600">{{ $candidate->mission }}</p>
                                </div>
                            </div>

                            @if(!Auth::user()->has_voted && $canVote && $candidate->is_active && $candidate->campaign_end_date > now())
                                <button type="button"
                                        class="vote-button w-full mt-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                        data-candidate-id="{{ $candidate->id }}"
                                        data-candidate-name="{{ $candidate->name }}">
                                    <span class="vote-text">Vote for {{ $candidate->name }}</span>
                                    <div class="loading-spinner ml-2 hidden">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Vote Confirmation Modal -->
    <div class="vote-confirmation-modal" id="voteConfirmationModal">
        <div class="fixed inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Your Vote</h3>
                        <p class="text-gray-600 mb-4">Are you sure you want to vote for <span id="selectedCandidateName"></span>? This action cannot be undone.</p>
                        <div class="flex justify-end space-x-4">
                            <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-900" onclick="closeVoteModal()">
                                Cancel
                            </button>
                            <form id="voteForm" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Confirm Vote
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize voting functionality
            initializeVoting();
        });

        function initializeVoting() {
            const voteButtons = document.querySelectorAll('.vote-button');
            const voteModal = document.getElementById('voteConfirmationModal');
            const selectedCandidateNameSpan = document.getElementById('selectedCandidateName');
            const voteForm = document.getElementById('voteForm');
            let currentCandidateId = null;
            let isSubmitting = false;

            // Show vote confirmation modal
            function showVoteModal(candidateId, candidateName) {
                currentCandidateId = candidateId;
                selectedCandidateNameSpan.textContent = candidateName;
                voteModal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }

            // Close vote confirmation modal
            window.closeVoteModal = function() {
                voteModal.style.display = 'none';
                currentCandidateId = null;
                document.body.style.overflow = 'auto'; // Restore scrolling
            }

            // Handle vote button clicks
            voteButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    const candidateId = e.currentTarget.dataset.candidateId;
                    const candidateName = e.currentTarget.dataset.candidateName;
                    showVoteModal(candidateId, candidateName);
                });
            });

            // Handle vote form submission
            voteForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                if (!currentCandidateId || isSubmitting) return;

                isSubmitting = true;

                // Update UI to show loading state
                const currentButton = document.querySelector(`.vote-button[data-candidate-id="${currentCandidateId}"]`);
                const buttonText = currentButton.querySelector('.vote-text');
                const spinner = currentButton.querySelector('.loading-spinner');
                const submitButton = voteForm.querySelector('button[type="submit"]');

                // Disable all interactive elements
                voteButtons.forEach(btn => btn.disabled = true);
                submitButton.disabled = true;
                buttonText.style.display = 'none';
                spinner.style.display = 'inline-block';

                try {
                    const response = await fetch(`/voting/vote/${currentCandidateId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ candidate_id: currentCandidateId })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'An error occurred while processing your vote.');
                    }

                    if (data.success) {
                        showAlert('success', data.message);
                        closeVoteModal();

                        // Redirect after a short delay
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } catch (error) {
                    showAlert('error', error.message);

                    // Reset UI state
                    voteButtons.forEach(btn => btn.disabled = false);
                    submitButton.disabled = false;
                    buttonText.style.display = 'block';
                    spinner.style.display = 'none';
                    closeVoteModal();
                } finally {
                    isSubmitting = false;
                }
            });

            // Handle modal close on outside click
            voteModal.addEventListener('click', (event) => {
                if (event.target === voteModal) {
                    closeVoteModal();
                }
            });

            // Handle modal close on escape key
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && voteModal.style.display === 'block') {
                    closeVoteModal();
                }
            });
        }

        // Alert handling function
        function showAlert(type, message) {
            // Remove any existing alerts
            const existingAlerts = document.querySelectorAll('.alert-message');
            existingAlerts.forEach(alert => alert.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert-message fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-50 text-green-800 border border-green-200'
                    : 'bg-red-50 text-red-800 border border-red-200'
            }`;

            alertDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success'
                        ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                        : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    }
                    </svg>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(alertDiv);

            // Add entrance animation
            setTimeout(() => alertDiv.classList.add('translate-y-1'), 10);

            // Remove the alert after 5 seconds
            setTimeout(() => {
                alertDiv.classList.add('opacity-0');
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }
    </script>
@endsection
