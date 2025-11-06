<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Analyzer - AI-Powered CV Analysis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">CV Analyzer</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">Welcome, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-semibold transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white min-h-screen shadow-lg">
            <div class="p-6">
                <nav class="space-y-2">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 bg-blue-50 text-blue-700 px-4 py-3 rounded-lg font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Analyze CV</span>
                    </a>
                    <a href="{{ route('cv.history') }}" class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-4 py-3 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Analysis History</span>
                    </a>
                    <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 text-gray-700 hover:bg-gray-100 px-4 py-3 rounded-lg font-semibold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>My Profile</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">CV Analysis</h2>
                <p class="text-gray-600">AI-Powered CV Analysis & Career Recommendations</p>
            </div>

            <!-- Main Form -->
            <div class="max-w-4xl bg-white rounded-xl shadow-md p-8">
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('analyze.cv') }}" method="POST" enctype="multipart/form-data" id="cvForm">
                    @csrf

                    <!-- CV Upload -->
                    <div class="mb-6">
                        <label for="cv_file" class="block text-sm font-semibold text-gray-700 mb-2">
                            Upload Your CV (PDF only)
                        </label>
                        
                        <!-- Upload Box -->
                        <div class="mt-1">
                            <!-- Default Upload State -->
                            <div id="uploadDefault" class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors cursor-pointer">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload your CV</span>
                                            <input id="cv_file" name="cv_file" type="file" accept=".pdf" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF up to 1MB</p>
                                </div>
                            </div>

                            <!-- Success Upload State -->
                            <div id="uploadSuccess" class="hidden flex justify-center px-6 pt-5 pb-6 border-2 border-green-300 border-dashed rounded-lg bg-green-50">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-sm text-green-700">
                                        <p class="font-medium">CV Successfully Uploaded!</p>
                                        <p id="fileNameSuccess" class="text-green-600"></p>
                                    </div>
                                    <button type="button" id="changeFileBtn" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                        Change File
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Error Message -->
                        <div id="fileError" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>

                    <!-- Job Title Selection -->
                    <div class="mb-6">
                        <label for="job_title_select" class="block text-sm font-semibold text-gray-700 mb-2">
                            Select Job Position <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="job_title_select"
                            name="job_title"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                            <option value="">-- Select a Job Position --</option>
                            @foreach($jobTitles as $job)
                                <option value="{{ $job->job_title }}">
                                    {{ $job->job_title }} - {{ $job->department }}
                                </option>
                            @endforeach
                            <option value="__ADD_NEW__" class="font-semibold text-blue-600">+ Add New Job Title</option>
                        </select>
                    </div>

                    <!-- New Job Title Input (Hidden by default) -->
                    <div id="newJobTitleSection" class="mb-6 hidden">
                        <label for="new_job_title" class="block text-sm font-semibold text-gray-700 mb-2">
                            New Job Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="new_job_title"
                            name="new_job_title"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="e.g., Senior Data Scientist, Marketing Manager, etc."
                            maxlength="255"
                            disabled
                        >
                        <p class="mt-1 text-xs text-gray-500">Enter the job title you want to analyze your CV against</p>

                        <!-- New Job Description -->
                        <label for="new_job_description" class="block text-sm font-semibold text-gray-700 mb-2 mt-4">
                            Job Description / Specifications <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="new_job_description"
                            name="new_job_description"
                            rows="8"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Enter the detailed job description, requirements, qualifications, and responsibilities..."
                            maxlength="5000"
                            disabled
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500">Describe the job requirements, skills needed, responsibilities, qualifications, etc.</p>
                    </div>

                    <!-- Job Requirements (Optional) -->
                    <div class="mb-6">
                        <label for="job_requirements" class="block text-sm font-semibold text-gray-700 mb-2">
                            Additional Job Requirements <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <textarea
                            id="job_requirements"
                            name="job_requirements"
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            placeholder="Add specific requirements, skills, or qualifications for more accurate analysis (optional)..."
                            maxlength="3000"
                        >{{ old('job_requirements') }}</textarea>
                        
                        <p class="mt-1 text-xs text-gray-500">
                            <svg class="inline w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            This field is optional. AI will use this as additional context for analysis.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            id="analyzeBtn"
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-700 transition shadow-lg inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled
                        >
                            <svg id="loadingIcon" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="btnText">Analyze CV with AI</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Features Section -->
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-8">What You'll Get</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-md p-6 text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">CV Score & Match %</h3>
                        <p class="text-gray-600 text-sm">Get an overall score and see how well your CV matches the job requirements</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6 text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Personalized Recommendations</h3>
                        <p class="text-gray-600 text-sm">Get specific courses, projects, and skills to improve your chances</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6 text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-2">Action Plan</h3>
                        <p class="text-gray-600 text-sm">Receive a timeline-based plan to improve your profile step by step</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Elements
        const cvFileInput = document.getElementById('cv_file');
        const uploadDefault = document.getElementById('uploadDefault');
        const uploadSuccess = document.getElementById('uploadSuccess');
        const fileNameSuccess = document.getElementById('fileNameSuccess');
        const changeFileBtn = document.getElementById('changeFileBtn');
        const fileError = document.getElementById('fileError');
        const jobTitleSelect = document.getElementById('job_title_select');
        const newJobTitleSection = document.getElementById('newJobTitleSection');
        const newJobTitleInput = document.getElementById('new_job_title');
        const newJobDescriptionInput = document.getElementById('new_job_description');
        const jobRequirements = document.getElementById('job_requirements');
        const analyzeBtn = document.getElementById('analyzeBtn');
        const cvForm = document.getElementById('cvForm');

        // Handle job title dropdown change
        jobTitleSelect.addEventListener('change', function() {
            if (this.value === '__ADD_NEW__') {
                // Show new job title input
                newJobTitleSection.classList.remove('hidden');
                newJobTitleInput.setAttribute('required', 'required');
                newJobTitleInput.removeAttribute('disabled');
                newJobDescriptionInput.setAttribute('required', 'required');
                newJobDescriptionInput.removeAttribute('disabled');
                // Keep dropdown at __ADD_NEW__ to pass validation
            } else {
                // Hide new job title input and DISABLE them so they won't be sent
                newJobTitleSection.classList.add('hidden');
                newJobTitleInput.removeAttribute('required');
                newJobTitleInput.setAttribute('disabled', 'disabled');
                newJobTitleInput.value = '';
                newJobDescriptionInput.removeAttribute('required');
                newJobDescriptionInput.setAttribute('disabled', 'disabled');
                newJobDescriptionInput.value = '';
            }
            updateSubmitButton();
        });

        // Update submit button state
        function updateSubmitButton() {
            const hasFile = cvFileInput.files.length > 0;
            const hasJobSelection = jobTitleSelect.value !== '' && jobTitleSelect.value !== '__ADD_NEW__';
            const hasNewJobTitle = newJobTitleInput.value.trim() !== '';
            const hasNewJobDescription = newJobDescriptionInput.value.trim() !== '';
            
            // Enable if: has file AND (has job selection OR (has new job title AND description))
            analyzeBtn.disabled = !(hasFile && (hasJobSelection || (hasNewJobTitle && hasNewJobDescription)));
        }

        // Handle file upload
        function handleFileUpload(file) {
            // Validate file type
            if (file.type !== 'application/pdf') {
                showFileError('Please upload a PDF file.');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                showFileError('File size must be less than 10MB.');
                return;
            }

            // Success - show success state
            hideFileError();
            fileNameSuccess.textContent = file.name;
            uploadDefault.classList.add('hidden');
            uploadSuccess.classList.remove('hidden');
            updateSubmitButton();
        }

        // Show file error
        function showFileError(message) {
            fileError.textContent = message;
            fileError.classList.remove('hidden');
            cvFileInput.value = '';
        }

        // Hide file error
        function hideFileError() {
            fileError.classList.add('hidden');
        }

        // Reset to default upload state
        function resetUploadState() {
            uploadDefault.classList.remove('hidden');
            uploadSuccess.classList.add('hidden');
            cvFileInput.value = '';
            hideFileError();
            updateSubmitButton();
        }

        // Event Listeners

        // File input change
        cvFileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0]);
            }
        });

        // Change file button
        changeFileBtn.addEventListener('click', resetUploadState);

        // New job title and description input change
        newJobTitleInput.addEventListener('input', updateSubmitButton);
        newJobDescriptionInput.addEventListener('input', updateSubmitButton);

        // Drag and drop functionality
        uploadDefault.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadDefault.classList.add('border-blue-400', 'bg-blue-50');
        });

        uploadDefault.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadDefault.classList.remove('border-blue-400', 'bg-blue-50');
        });

        uploadDefault.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadDefault.classList.remove('border-blue-400', 'bg-blue-50');
            
            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                cvFileInput.files = e.dataTransfer.files;
                handleFileUpload(file);
            }
        });

        // Click on upload area to trigger file input
        uploadDefault.addEventListener('click', function() {
            cvFileInput.click();
        });

        // Form submission handling
        cvForm.addEventListener('submit', function() {
            const btn = document.getElementById('analyzeBtn');
            const loadingIcon = document.getElementById('loadingIcon');
            const btnText = document.getElementById('btnText');

            btn.disabled = true;
            loadingIcon.classList.remove('hidden');
            btnText.textContent = 'Analyzing with AI...';

            // Re-enable after timeout (in case of error)
            setTimeout(() => {
                btn.disabled = false;
                loadingIcon.classList.add('hidden');
                btnText.textContent = 'Analyze CV with AI';
            }, 60000);
        });
    </script>
</body>
</html>