@extends('layouts.app')

@section('meta')
    <meta name="description" content="Edit Candidate - Student Election Admin">
@endsection

@section('title')
    <title>Edit Candidate</title>
@endsection

@include('layouts.navigation')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Edit Candidate: {{ $candidate->name }}
                    </h2>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg">
                <form action="{{ route('admin.update', $candidate) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Student ID -->
                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                            <input type="text" name="student_id" id="student_id" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('student_id', $candidate->student_id) }}">
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('name', $candidate->name) }}">
                        </div>

                        <!-- Faculty -->
                        <div>
                            <label for="faculty" class="block text-sm font-medium text-gray-700">Faculty</label>
                            <select name="faculty" id="faculty" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Faculty</option>
                                <option value="FCVAC" {{ old('faculty', $candidate->faculty) == 'FCVAC' ? 'selected' : '' }}>Faculty Communication, Visual, Art and Computing</option>
                                <option value="FBA" {{ old('faculty', $candidate->faculty) == 'FBA' ? 'selected' : '' }}>Faculty of Business and Accountancy</option>
                                <option value="FESS" {{ old('faculty', $candidate->faculty) == 'FESS' ? 'selected' : '' }}>Faculty of Education and Social Sciences</option>
                                <option value="FE" {{ old('faculty', $candidate->faculty) == 'FE' ? 'selected' : '' }}>Faculty of Engineering</option>
                            </select>
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                            <select name="position" id="position" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Position</option>
                                <option value="President" {{ old('position', $candidate->position) == 'President' ? 'selected' : '' }}>President</option>
                                <option value="Vice President" {{ old('position', $candidate->position) == 'Vice President' ? 'selected' : '' }}>Vice President</option>
                                <option value="Secretary" {{ old('position', $candidate->position) == 'Secretary' ? 'selected' : '' }}>Secretary</option>
                                <option value="Treasurer" {{ old('position', $candidate->position) == 'Treasurer' ? 'selected' : '' }}>Treasurer</option>
                            </select>
                        </div>

                        <!-- Campaign Period -->

                        <div>
                            <label for="campaign_start_date" class="block text-sm font-medium text-gray-700">Campaign Start Date</label>
                            <input type="date" name="campaign_start_date" id="campaign_start_date"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('campaign_start_date', $candidate->campaign_start_date ? \Carbon\Carbon::parse($candidate->campaign_start_date)->format('Y-m-d') : '') }}">
                        </div>

                        <div>
                            <label for="campaign_end_date" class="block text-sm font-medium text-gray-700">Campaign End Date</label>
                            <input type="date" name="campaign_end_date" id="campaign_end_date"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('campaign_end_date', $candidate->campaign_end_date ? \Carbon\Carbon::parse($candidate->campaign_end_date)->format('Y-m-d') : '') }}">
                        </div>

                    <!-- Vision -->
                    <div>
                        <label for="vision" class="block text-sm font-medium text-gray-700">Vision</label>
                        <textarea name="vision" id="vision" rows="3"
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('vision', $candidate->vision) }}</textarea>
                    </div>

                    <!-- Mission -->
                    <div>
                        <label for="mission" class="block text-sm font-medium text-gray-700">Mission</label>
                        <textarea name="mission" id="mission" rows="3"
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('mission', $candidate->mission) }}</textarea>
                    </div>

                    <!-- Current Image Display -->
{{--                    @if($candidate->image_path)--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>--}}
{{--                            <img src="{{ Storage::url($candidate->image_path) }}" alt="{{ $candidate->name }}" class="h-32 w-32 object-cover rounded-md">--}}
{{--                        </div>--}}
{{--                    @endif--}}


                    <!-- Image Upload -->
                    <div x-data="imageUpload">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Candidate Image</label>

                        <!-- Current Image Display -->
                        @if($candidate->image_path)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 mb-2">Current Image:</p>
                                <img src="{{ Storage::url($candidate->image_path) }}"
                                     alt="{{ $candidate->name }}"
                                     class="h-32 w-32 object-cover rounded-md">
                            </div>
                        @endif

                        <!-- New Image Upload -->
                        <div class="mt-2">
                            <div class="flex items-center">
                                <input type="file"
                                       name="image"
                                       id="image"
                                       accept="image/*"
                                       class="sr-only"
                                       @change="handleImageChange">
                                <label for="image"
                                       class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Choose New Image
                                </label>
                            </div>

                            <!-- Preview for new image -->
                            <div x-show="imageUrl" class="mt-4">
                                <p class="text-sm text-gray-500 mb-2">New Image Preview:</p>
                                <img :src="imageUrl"
                                     alt="Preview"
                                     class="h-32 w-32 object-cover rounded-md">
                            </div>

                            <!-- Error message -->
                            <p x-show="error"
                               x-text="error"
                               class="mt-2 text-sm text-red-600"></p>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox"
                                   name="is_active"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="1"
                                {{ $candidate->is_active ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-600">Active Candidate</span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update Candidate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function imageUpload() {
            return {
                imageUrl: null,
                error: null,
                handleImageChange(event) {
                    const file = event.target.files[0];
                    this.error = null;

                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            this.error = 'File size must be less than 2MB';
                            event.target.value = '';
                            this.imageUrl = null;
                            return;
                        }

                        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                            this.error = 'File must be an image (JPG, PNG, or GIF)';
                            event.target.value = '';
                            this.imageUrl = null;
                            return;
                        }

                        const reader = new FileReader();
                        reader.onloadend = () => {
                            this.imageUrl = reader.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.imageUrl = null;
                    }
                }
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('campaign_start_date');
            const endDate = document.getElementById('campaign_end_date');

            // Set min date for end date when start date changes
            startDate.addEventListener('change', function() {
                endDate.min = this.value;
                if (endDate.value && endDate.value < this.value) {
                    endDate.value = this.value;
                }
            });

            // Set max date for start date when end date changes
            endDate.addEventListener('change', function() {
                startDate.max = this.value;
                if (startDate.value && startDate.value > this.value) {
                    startDate.value = this.value;
                }
            });

            // Set initial min/max if dates are already set
            if (startDate.value) {
                endDate.min = startDate.value;
            }
            if (endDate.value) {
                startDate.max = endDate.value;
            }
        });
    </script>
@endsection
