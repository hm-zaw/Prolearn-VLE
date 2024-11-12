<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    public function create(Quiz $quiz)
    {
        return view('questioncreate', ['quiz' => $quiz]);
    }

    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'option1' => 'required|string|max:255',
            'option2' => 'required|string|max:255',
            'option3' => 'required|string|max:255',
            'option4' => 'required|string|max:255',
            'correct_answer' => 'required|string|max:255',
        ]);

        $quiz->questions()->create($validated);

        return view('questioncreate', ['quiz' => $quiz])->with('success', 'Question added successfully.');
    }

      // Edit a question
      public function edit(Quiz $quiz, Question $question)
      {
          return response()->json($question);
      }

      // Update a question
      public function update(Request $request, Quiz $quiz, Question $question)
      {
          $request->validate([
              'question' => 'required',
              'option1' => 'required',
              'option2' => 'required',
              'option3' => 'required',
              'option4' => 'required',
              'correct_answer' => 'required',
          ]);

          $question->update($request->all());

          return response()->json(['success' => true]);
      }


      // Delete a question
      public function destroy(Quiz $quiz, Question $question)
      {
          $question->delete();

          return redirect()->back();
      }
}

