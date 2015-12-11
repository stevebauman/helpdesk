<?php

namespace App\Processors\ActiveDirectory;

use App\Http\Presenters\ActiveDirectory\SetupQuestionPresenter;
use App\Http\Requests\ActiveDirectory\SetupQuestionRequest;
use App\Models\Question;
use App\Models\User;
use App\Processors\Processor;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Encryption\Encrypter;

class SetupQuestionProcessor extends Processor
{
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var Question
     */
    protected $question;

    /**
     * @var Encrypter
     */
    protected $encrypter;

    /**
     * @var SetupQuestionPresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param Guard                  $guard
     * @param Question               $question
     * @param Encrypter              $encrypter
     * @param SetupQuestionPresenter $presenter
     */
    public function __construct(Guard $guard, Question $question, Encrypter $encrypter, SetupQuestionPresenter $presenter)
    {
        $this->guard = $guard;
        $this->question = $question;
        $this->encrypter = $encrypter;
        $this->presenter = $presenter;
    }

    /**
     * Displays the table of the current users security questions.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = $this->guard->user();

        if ($user instanceof User) {
            $finished = (count($user->questions) >= 3 ? true : false);

            $questions = $this->presenter->table($user);

            return view('pages.active-directory.questions.setup.index', compact('questions', 'finished'));
        }

        abort(404);
    }

    /**
     * Displays the users security question.
     *
     * @param int|string $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = $this->guard->user();

        if ($user instanceof User) {
            $question = $user->questions()->findOrFail($id);

            $form = $this->presenter->form($user, $question);

            $answer = $this->encrypter->decrypt($question->pivot->answer);

            return view('pages.active-directory.questions.setup.edit', compact('form', 'answer'));
        }

        abort(404);
    }

    /**
     * Updates the specified security question.
     *
     * @param SetupQuestionRequest $request
     * @param int|string           $id
     *
     * @return bool
     */
    public function update(SetupQuestionRequest $request, $id)
    {
        $user = $this->guard->user();

        if ($user instanceof User) {
            $user->questions()->detach([$id]);

            return $this->save($request);
        }

        return false;
    }

    /**
     * Displays the form to setup security questions.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Illuminate\View\View
     */
    public function setup()
    {
        $user = $this->guard->user();

        if ($user instanceof User) {
            $form = $this->presenter->form($user, $this->question);

            // Add one to the question count to indicate
            // the current step is in progress.
            $step = count($user->questions) + 1;

            return view('pages.active-directory.questions.setup.step', compact('form', 'step'));
        }

        abort(404);
    }

    /**
     * Saves a users security question.
     *
     * @param SetupQuestionRequest $request
     *
     * @return bool
     */
    public function save(SetupQuestionRequest $request)
    {
        $user = $this->guard->user();

        if ($user instanceof User) {
            $question = $this->question->findOrFail($request->input('question'));

            $answer = $this->encrypter->encrypt($request->input('answer'));

            if ($user->questions()->save($question, ['answer' => $answer])) {
                return true;
            }
        }

        return false;
    }
}
