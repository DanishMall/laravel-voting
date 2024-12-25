<div x-data="imageUpload()" class="space-y-4">
    <label class="block text-sm font-medium text-gray-700">Candidate Photo</label>

    <div class="flex items-center space-x-6">
        <!-- Image Preview Area -->
        <div class="h-40 w-40 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center border-2 border-gray-300">
            <template x-if="imageUrl">
                <img
                    :src="imageUrl"
                    alt="Candidate preview"
                    class="h-full w-full object-cover"
                />
            </template>
            <template x-if="!imageUrl">
                <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </template>
        </div>

        <!-- Upload Button Area -->
        <div class="flex flex-col space-y-2">
            <label
                for="image"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                </svg>
                Upload Photo
                <input
                    type="file"
                    id="image"
                    name="image"
                    class="sr-only"
                    accept="image/*"
                    @change="handleImageChange($event)"
                >
            </label>
            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
            <template x-if="error">
                <p class="text-sm text-red-600" x-text="error"></p>
            </template>
        </div>
    </div>
</div>
