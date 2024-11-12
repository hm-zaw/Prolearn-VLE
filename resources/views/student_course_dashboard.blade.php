<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }}</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Preline UI CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@preline/plugin@1.7.8/dist/preline.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <!-- Main container -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-white shadow-md hidden sm:block">
            <div class="h-full">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-bold">Course Chapters</h2>
                </div>
                <nav class="p-4">
                    <ul id="chapterList" class="space-y-2">
                        @foreach($course->chapters as $index => $chapter)
                            <li>
                                <a href="javascript:void(0);" onclick="loadChapter({{ $index }});" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-gray-100">
                                    <i class="fas fa-book mr-2 text-blue-500"></i> {{ $chapter->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-6 overflow-auto">
            <button id="sidebarToggle" class="sm:hidden p-2 bg-blue-500 text-white rounded-lg mb-4">
                <i class="fas fa-bars"></i> Toggle Sidebar
            </button>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h1 id="chapterTitle" class="text-2xl font-bold">{{ $course->chapters->first()->title }}</h1>
                    <div class="flex space-x-2">
                        <!-- Check if the user is a teacher -->
                        @if(auth()->user()->role->name == 'teacher')
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700" onclick="document.getElementById('videoUpload').click();">
                                <i class="fas fa-upload"></i> Upload Video
                            </button>
                            <input type="file" id="videoUpload" class="hidden" onchange="uploadVideo({{ $course->chapters->first()->id }})">
                        @else
                            <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-check-circle"></i> Mark as Complete
                            </button>
                        @endif
                        <button class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-download"></i> Download Materials
                        </button>
                    </div>
                </div>
                <div class="relative">
                    <video id="chapterVideo" class="w-full h-auto rounded-lg shadow-md" controls>
                        <source src="{{ $course->chapters->first()->video_path ? asset('storage/' . $course->chapters->first()->video_path) : $course->chapters->first()->video_url }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <div class="mt-4">
                    <div class="w-full bg-gray-300 rounded-full h-2.5">
                        <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $course->chapters->first()->progress }}%"></div>
                    </div>
                    <p id="progressText" class="text-sm text-gray-600 mt-2">Progress: {{ $course->chapters->first()->progress }}%</p>
                </div>
            </div>

            <!-- Assignments and Quizzes section -->
            <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold">Assignments and Quizzes</h2>
                <div id="assignmentsQuizzes"></div>
            </div>

            <div class="mt-4 flex justify-between">
                <button id="prevChapter" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50" disabled>
                    <i class="fas fa-arrow-left"></i> Previous Chapter
                </button>
                <button id="nextChapter" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50">
                    Next Chapter <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </main>
    </div>

    <script>
        let chapters = @json($course->chapters);
        let currentChapter = 0;

        function loadChapter(index) {
            const chapter = chapters[index];
            document.getElementById('chapterTitle').textContent = chapter.title;
            document.getElementById('chapterVideo').src = chapter.video_path ? '{{ asset("storage/") }}/' + chapter.video_path : chapter.video_url;
            document.getElementById('progressBar').style.width = chapter.progress + '%';
            document.getElementById('progressText').textContent = 'Progress: ' + chapter.progress + '%';

            const assignmentsQuizzes = document.getElementById('assignmentsQuizzes');
            assignmentsQuizzes.innerHTML = '';

            if (chapter.assignments.length === 0 && chapter.quizzes.length === 0) {
                assignmentsQuizzes.innerHTML = '<p class="text-sm text-gray-500">No assignments or quizzes available for this chapter.</p>';
            } else {
                chapter.assignments.forEach(assignment => {
                    const assignmentDiv = document.createElement('div');
                    assignmentDiv.className = 'mt-2 flex items-center justify-between bg-gray-50 p-3 rounded-lg';
                    assignmentDiv.innerHTML = `
                        <span class="flex items-center">
                            <i class="fas fa-tasks mr-2 text-yellow-500"></i>
                            Assignment: ${assignment.name}
                        </span>
                        @if(auth()->user()->role->name == 'teacher')
                            <button class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700">Check Assignment</button>
                        @else
                            <button class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700">Submit</button>
                        @endif
                    `;
                    assignmentsQuizzes.appendChild(assignmentDiv);
                });

                const chapterQuizzes = @json($chapter->quizzes); // Pass quizzes data to JavaScript
                const userRole = "{{ auth()->user()->role->name }}"; // Get user's role

                chapterQuizzes.forEach(quiz => {
    const quizDiv = document.createElement('div');
    quizDiv.className = 'mt-2 flex items-center justify-between bg-gray-50 p-3 rounded-lg shadow-sm';
    let quizHTML = `
        <span class="flex items-center">
            <i class="fas fa-question-circle mr-2 text-yellow-500"></i>
            <span class="font-medium">${quiz.title}</span>
        </span>
    `;

    if (userRole === 'teacher') {
        quizHTML += `
            <div class="flex space-x-2">
                <a href="/quiz/${quiz.id}/results">
                    <button class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700">View Results</button>
                </a>
                <a href="/quiz/${quiz.id}/questions/create">
                    <button class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700">Edit Quiz</button>
                </a>
            </div>
        `;
    } else {
        quizHTML += `
            <a href="/quiz/${quiz.id}/take">
                <button class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-700">Take Quiz</button>
            </a>
        `;
    }

    quizDiv.innerHTML = quizHTML;
    document.getElementById('assignmentsQuizzes').appendChild(quizDiv);
});
            }

            currentChapter = index;
            document.getElementById('prevChapter').disabled = index === 0;
            document.getElementById('nextChapter').disabled = index === chapters.length - 1;
        }

        document.getElementById('prevChapter').addEventListener('click', () => {
            if (currentChapter > 0) {
                loadChapter(currentChapter - 1);
            }
        });

        document.getElementById('nextChapter').addEventListener('click', () => {
            if (currentChapter < chapters.length - 1) {
                loadChapter(currentChapter + 1);
            }
        });

        function uploadVideo(chapterId) {
            const fileInput = document.getElementById('videoUpload');
            const formData = new FormData();
            formData.append('video', fileInput.files[0]);

            fetch(`/chapters/${chapterId}/upload-video`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('chapterVideo').src = data.video_path;
                } else {
                    alert('Video upload failed!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during the upload.');
            });
        }
    </script>
</body>
</html>
