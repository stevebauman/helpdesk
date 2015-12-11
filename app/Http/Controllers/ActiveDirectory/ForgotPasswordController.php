<?php

namespace App\Http\Controllers\ActiveDirectory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActiveDirectory\ForgotPassword\DiscoverRequest;
use App\Http\Requests\ActiveDirectory\ForgotPassword\PasswordRequest;
use App\Http\Requests\ActiveDirectory\ForgotPassword\QuestionRequest;
use App\Processors\ActiveDirectory\ForgotPasswordProcessor;
use Illuminate\Contracts\View\View;

class ForgotPasswordController extends Controller
{
    /**
     * @var ForgotPasswordProcessor
     */
    protected $processor;

    /**
     * Constructor.
     *
     * @param ForgotPasswordProcessor $processor
     */
    public function __construct(ForgotPasswordProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Displays the form to reset a users password.
     *
     * @return \Illuminate\View\View
     */
    public function discover()
    {
        return $this->processor->discover();
    }

    /**
     * Discovers and creates a forgot password
     * token for the submitted username.
     *
     * @param DiscoverRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function find(DiscoverRequest $request)
    {
        $token = $this->processor->find($request);

        if (is_string($token)) {
            return redirect()->route('auth.forgot-password.questions', [$token]);
        }

        $message = "We couldn't locate the user you're looking for. Try again!";

        flash()->setTimer(false)->error('Error', $message);

        return redirect()->route('auth.forgot-password.discover');
    }

    /**
     * Displays the form for answering the users security questions.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function questions($token = '')
    {
        $view = $this->processor->questions($token);

        if ($view instanceof View) {
            return $view;
        }

        return redirect()->route('auth.forgot-password.discover');
    }

    /**
     * Processes the submitted question answers and generates
     * a reset token if all answers are correct.
     *
     * @param QuestionRequest $request
     * @param string          $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function answer(QuestionRequest $request, $token)
    {
        $reset = $this->processor->answer($request, $token);

        if (is_string($reset)) {
            return redirect()->route('auth.forgot-password.reset', [$reset]);
        }

        $message = 'Hmmm, it looks there was an issue with one of your answers. Try again!';

        flash()->error('Error', $message);

        return redirect()->route('auth.forgot-password.questions', [$token])->withInput($request->all());
    }

    /**
     * Displays the form for resetting a users password by the specified token.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function reset($token)
    {
        $view = $this->processor->reset($token);

        if ($view instanceof View) {
            return $view;
        }

        return redirect()->route('auth.forgot-password.discover');
    }

    /**
     * Changes the users password by the specified token.
     *
     * @param PasswordRequest $request
     * @param string          $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change(PasswordRequest $request, $token)
    {
        if ($this->processor->change($request, $token)) {
            flash()->success('Success!', 'Successfully changed password. You can now login with your new password.');

            return redirect()->route('auth.login.index');
        } else {
            flash()->error('Error!', 'There was an issue changing your password. Please try again.');

            return redirect()->route('auth.forgot-password.reset', [$token]);
        }
    }
}
