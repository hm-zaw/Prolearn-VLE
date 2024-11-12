<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function create()
    {
        return view('createcourse');  // Render the single Blade file
    }

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
            'chapters' => 'array',
            'chapters.*' => 'string|max:255',
            'quizzes' => 'array',
            'quizzes.*' => 'array', // Validate quizzes as an array of arrays
            'quizzes.*.*' => 'string|max:255', // Validate each quiz title
            'assignments' => 'array',
            'assignments.*.*' => 'string|max:255',
        ]);

        // Handle the file upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('course_images', 'public');
        }

        // Create the course
        $course = Course::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'] ?? null,
            'major_id' => $validatedData['category'] ?? null,
            'user_id' => Auth::user()->id,
            'image' => $imagePath,
        ]);

        // Create chapters, quizzes, and assignments
        if (!empty($validatedData['chapters'])) {
            foreach ($validatedData['chapters'] as $index => $chapterTitle) {
                // Create a chapter
                $chapter = $course->chapters()->create([
                    'title' => $chapterTitle,
                    'order' => $index,  // Assuming you want to store the order
                    'user_id' => Auth::user()->id,
                ]);

                // Create quizzes for this chapter
                if (isset($validatedData['quizzes'][$index])) {
                    foreach ($validatedData['quizzes'][$index] as $quizTitle) {
                        $chapter->quizzes()->create([
                            'title' => $quizTitle,
                        ]);
                    }
                }

                // Create assignments for this chapter
                if (isset($validatedData['assignments'][$index])) {
                    foreach ($validatedData['assignments'][$index] as $assignmentName) {
                        Assignment::create([
                            'name' => $assignmentName,
                            'course_id' => $course->id,
                            'chapter_id' => $chapter->id,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('courses.create')->with('success', 'Course created successfully!');
    }

    public function show($id)
    {
        $course = Course::with('chapters')->findOrFail($id);

        // Calculate the number of chapters
        $chapterCount = $course->chapters->count();

        return view('courseshow', compact('course', 'chapterCount'));
    }

    public function filterByMajor($majorId)
    {
        $courses = Course::where('major_id', $majorId)->get();
        return view('test', compact('courses'));
    }

    public function enroll(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        DB::table('student_enrolled_courses')->insert([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('courseshow', $courseId)->with('success', 'You have successfully enrolled in the course!');
    }

    public function showCourses()
    {
        $allCourses = Course::withCount('chapters')->get();

        // Pass the data to the view
        return view('dashboard', compact('allCourses'));
    }

    public function showDetail($id)
    {
        $course = Course::with('chapters.quizzes', 'chapters.assignments')->findOrFail($id);

        // Calculate the number of chapters
        $chapterCount = $course->chapters->count();

        return view('student_course_dashboard', compact('course', 'chapterCount'));
    }

    public function uploadVideo(Request $request, $chapterId)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,avi,mov|max:20000', // Validate the video file
        ]);

        $chapter = Chapter::findOrFail($chapterId);

        // Store the video file
        $videoPath = $request->file('video')->store('chapter_videos', 'public');

        // Update the chapter's video_path
        $chapter->update([
            'video_path' => $videoPath,
        ]);

        return response()->json([
            'success' => true,
            'video_path' => asset('storage/' . $videoPath),
        ]);
    }

    public function createEvent(Request $request){
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'string|max:255',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'passcode' => 'required|string|max:255',
            'link' => 'required|string|max:255',
            'recruiter_id' => 'required|integer',
        ]);

        $eventImagePath = null;
        if ($request->hasFile('image')) {
            $eventImagePath = $request->file('image')->store('course_images', 'public');
        }

        $event = Event::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category' => $validatedData['category'],
            'image' => $eventImagePath,
            'date' => $validatedData['date'],
            'time' => $validatedData['time'],
            'passcode' => $validatedData['passcode'],
            'link' => $validatedData['link'],
            'recruiter_id' => $validatedData['recruiter_id'],
        ]);

        return redirect()->back()->with('success', 'Event created successfully!');
    }

    public function showEventForm(){
        return view('create-event');
    }

    public function showEventToTr(){
        return view('create-event-teacher');
    }

    public function showCourseForm(){
        return view('create-course');
    }
}
