<?php

namespace App\Processors\ActiveDirectory;

use App\Http\Presenters\ActiveDirectory\QuestionPresenter;
use App\Http\Requests\ActiveDirectory\QuestionRequest;
use App\Models\Question;
use App\Processors\Processor;
use Illuminate\Support\Facades\Artisan;
use QuestionSeeder;

class QuestionProcessor extends Processor
{
    /**
     * @var Question
     */
    protected $question;

    /**
     * @var QuestionPresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param Question          $question
     * @param QuestionPresenter $presenter
     */
    public function __construct(Question $question, QuestionPresenter $presenter)
    {
        $this->question = $question;
        $this->presenter = $presenter;
    }

    /**
     * Displays all active directory security questions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $questions = $this->presenter->table($this->question);

        $navbar = $this->presenter->navbar();

        return view('pages.active-directory.questions.index', compact('questions', 'navbar'));
    }

    /**
     * Displays the form for creating a new security question.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $form = $this->presenter->form($this->question);

        return view('pages.active-directory.questions.create', compact('form'));
    }

    /**
     * Creates a new security question.
     *
     * @param QuestionRequest $request
     *
     * @return bool
     */
    public function store(QuestionRequest $request)
    {
        $question = $this->question->newInstance();

        $question->content = $request->input('content');

        return $question->save();
    }

    /**
     * Displays the form for editing the specified security question.
     *
     * @param int|string $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $question = $this->question->findOrFail($id);

        $form = $this->presenter->form($question);

        return view('pages.active-directory.questions.edit', compact('form', 'question'));
    }

    /**
     * Updates the specified security question.
     *
     * @param QuestionRequest $request
     * @param int|string      $id
     *
     * @return bool
     */
    public function update(QuestionRequest $request, $id)
    {
        $question = $this->question->findOrFail($id);

        $question->content = $request->input('content', $question->content);

        return $question->save();
    }

    /**
     * Deletes the specified security question.
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function destroy($id)
    {
        $question = $this->question->findOrFail($id);

        // Make sure we detach all of the users questions
        // due to foreign key restrictions.
        $question->users()->detach();

        return $question->delete();
    }

    /**
     * Creates default security questions.
     *
     * @return mixed
     */
    public function seed()
    {
        return Artisan::call('db:seed', [
            'class' => QuestionSeeder::class,
        ]);
    }
}
