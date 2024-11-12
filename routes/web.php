<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\dashboard;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\SocialiteController;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('test', function () {
    return view('test');
});

Route::get('displaycourse', function() {
    return view('courseshow');
});

// Course Routes
Route::post('/course/{course}/enroll', [CourseController::class, 'enroll'])->name('enroll');
Route::get('/courses/major/{major}', [CourseController::class, 'filterByMajor'])->name('courses.byMajor');

Route::get('/courseshow/{id}', [CourseController::class, 'showdetail'])->name('courseshow'); // Ensure the route name is `courseshow`

Route::get('/course/{id}', [CourseController::class, 'show'])->name('course.show');

Route::get('/createcourse', [CourseController::class, 'create'])->name('courses.create');
Route::post('/createcourse', [CourseController::class, 'store'])->name('courses.store');

// to send to hpl
Route::post('/createEvent', [CourseController::class, 'createEvent']);
Route::get('/create-event', [CourseController::class, 'showEventForm']);
Route::get('/create-event-teacher', [CourseController::class, 'showEventToTr']);
Route::get('/create-course-teacher', [CourseController::class, 'showCourseForm']);

Route::post('/chapters/{chapter}/upload-video', [CourseController::class, 'uploadVideo']);

Route::get('/quiz/{quiz}/questions/create', [QuizQuestionController::class, 'create'])->name('questions.create');
Route::post('/quiz/{quiz}/questions', [QuizQuestionController::class, 'store'])->name('questions.store');
Route::get('/quizzes/{quiz}/questions/{question}/edit', [QuizQuestionController::class, 'edit'])->name('questions.edit');
Route::put('/quizzes/{quiz}/questions/{question}', [QuizQuestionController::class, 'update'])->name('questions.update');
Route::delete('/quizzes/{quiz}/questions/{question}', [QuizQuestionController::class, 'destroy'])->name('questions.destroy');

Route::get('/eachcourse', function () {
    return view('student_course_dashboard');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register');

    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');

    Route::get('login/google', [SocialiteController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('login/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

    Route::get('login/github', [SocialiteController::class, 'redirectToGithub'])->name('login.github');
    Route::get('login/github/callback', [SocialiteController::class, 'handleGithubCallback']);
});

// Protected Routes (Require Authentication)
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Skills and Experiences Routes
    Route::post('/profile/skill/add', [ProfileController::class, 'addSkill'])->name('profile.skill.add');
    Route::delete('/profile/skill/{id}', [ProfileController::class, 'deleteSkill'])->name('profile.skill.delete');
    Route::post('/profile/experience/add', [ProfileController::class, 'addExperience'])->name('profile.experience.add');
    Route::delete('/profile/experience/{id}', [ProfileController::class, 'deleteExperience'])->name('profile.experience.delete');

    // Logout Route
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});

Route::post('/create_recruiter', [AdminController::class, 'create_recruiter']);
Route::post('/create_teacher', [AdminController::class, 'create_teacher']);
Route::get('/upgrade_major/{id}', [AdminController::class, 'upgrade_major']);
Route::get('/delete_user/{id}', [AdminController::class, 'delete_user']);

// Load additional auth routes
require __DIR__ . '/auth.php';
