@extends('layouts.app')

@section('meta')
    <meta name="description" content="Create New Candidate - Student Election Admin">
@endsection

@section('title')
    <title>Create New Candidate</title>
@endsection

@include('layouts.navigation')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Create New Candidate
                    </h2>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg">
                <form action="{{ route('admin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                    @csrf

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
                                   value="{{ old('student_id') }}">
                        </div>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('name') }}">
                        </div>

                        <!-- Faculty -->
                        <div>
                            <label for="faculty" class="block text-sm font-medium text-gray-700">Faculty</label>
                            <select name="faculty" id="faculty" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Faculty</option>
                                <option value="FCVAC" {{ old('faculty') == 'FCVAC' ? 'selected' : '' }}>Faculty Communication, Visual, Art and Computing</option>
                                <option value="FBA" {{ old('faculty') == 'FBA' ? 'selected' : '' }}>Faculty of Business and Accountancy</option>
                                <option value="FESS" {{ old('faculty') == 'FESS' ? 'selected' : '' }}>Faculty of Education and Social Sciences</option>
                                <option value="FE" {{ old('faculty') == 'FE' ? 'selected' : '' }}>Faculty of Engineering</option>
{{--                                @foreach($faculties as $faculty)--}}
{{--                                    <option value="{{ $faculty->id }}" {{ old('faculty') == $faculty->id ? 'selected' : '' }}>--}}
{{--                                        {{ $faculty->name }}--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
                            </select>
                        </div>

                        <!-- Position -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                            <select name="position" id="position" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Position</option>
                                <option value="President" {{ old('position') == 'President' ? 'selected' : '' }}>President</option>
                                <option value="Vice President" {{ old('position') == 'Vice President' ? 'selected' : '' }}>Vice President</option>
                                <option value="Secretary" {{ old('position') == 'Secretary' ? 'selected' : '' }}>Secretary</option>
                                <option value="Treasurer" {{ old('position') == 'Treasurer' ? 'selected' : '' }}>Treasurer</option>
                            </select>
                        </div>

                        <!-- Campaign Period -->
                        <div>
                            <label for="campaign_start_date" class="block text-sm font-medium text-gray-700">Campaign Start Date</label>
                            <input type="date" name="campaign_start_date" id="campaign_start_date"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('campaign_start_date') }}">
                        </div>

                        <div>
                            <label for="campaign_end_date" class="block text-sm font-medium text-gray-700">Campaign End Date</label>
                            <input type="date" name="campaign_end_date" id="campaign_end_date"
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('campaign_end_date') }}">
                        </div>
                    </div>

                    <!-- Vision -->
                    <div>
                        <label for="vision" class="block text-sm font-medium text-gray-700">Vision</label>
                        <textarea name="vision" id="vision" rows="3"
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('vision') }}</textarea>
                    </div>

                    <!-- Mission -->
                    <div>
                        <label for="mission" class="block text-sm font-medium text-gray-700">Mission</label>
                        <textarea name="mission" id="mission" rows="3"
                                  class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('mission') }}</textarea>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <x-image-upload-preview />
                        @error('image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Candidate
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
@endsection
