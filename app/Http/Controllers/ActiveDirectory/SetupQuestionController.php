<?php

namespace App\Http\Controllers\ActiveDirectory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActiveDirectory\SetupQuestionRequest;
use App\Processors\ActiveDirectory\SetupQuestionProcessor;

class SetupQuestionController extends Controller
{
    /**
     * @var SetupQuestionProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param SetupQuestionProcessor $processor
     */
    public function __construct(SetupQuestionProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays all of the users security questions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->processor->index();
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
        return $this->processor->edit($id);
    }

    /**
     * Updates the specified security question.
     *
     * @param SetupQuestionRequest $request
     * @param int|string           $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SetupQuestionRequest $request, $id)
    {
        if ($this->processor->update($request, $id)) {
            flash()->success('Success!', 'Successfully updated security question.');

            return redirect()->route('security-questions.index');
        } else {
            flash()->error('Error!', 'There was an issue updating this security question. Please try again.');

            return redirect()->route('security-questions.edit', [$id]);
        }
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
        return $this->processor->setup();
    }

    /**
     * Saves a users security question.
     *
     * @param SetupQuestionRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(SetupQuestionRequest $request)
    {
        if ($this->processor->save($request)) {
            flash()->success('Success!', 'Successfully saved security question.');

            return redirect()->back();
        } else {
            flash()->error('Error!', 'There was an issue saving this security question. Please try again.');

            return redirect()->back();
        }
    }
}
