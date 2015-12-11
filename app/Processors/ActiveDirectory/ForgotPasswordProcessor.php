<?php

namespace App\Processors\ActiveDirectory;

use Adldap\Contracts\Adldap;
use Adldap\Models\User as AdldapUser;
use App\Http\Presenters\ActiveDirectory\ForgotPasswordPresenter;
use App\Http\Requests\ActiveDirectory\ForgotPassword\DiscoverRequest;
use App\Http\Requests\ActiveDirectory\ForgotPassword\PasswordRequest;
use App\Http\Requests\ActiveDirectory\ForgotPassword\QuestionRequest;
use App\Jobs\Com\User\ChangePassword;
use App\Models\User;
use App\Processors\Processor;
use Illuminate\Contracts\Encryption\Encrypter;

class ForgotPasswordProcessor extends Processor
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Adldap
     */
    protected $adldap;

    /**
     * @var Encrypter
     */
    protected $encrypter;

    /**
     * @var ForgotPasswordPresenter
     */
    protected $presenter;

    /**
     * Constructor.
     *
     * @param User                    $user
     * @param Adldap                  $adldap
     * @param Encrypter               $encrypter
     * @param ForgotPasswordPresenter $presenter
     */
    public function __construct(User $user, Adldap $adldap, Encrypter $encrypter, ForgotPasswordPresenter $presenter)
    {
        $this->user = $user;
        $this->adldap = $adldap;
        $this->encrypter = $encrypter;
        $this->presenter = $presenter;
    }

    /**
     * Displays the form to reset a users password.
     *
     * @return \Illuminate\View\View
     */
    public function discover()
    {
        $form = $this->presenter->form();

        return view('pages.forgot-password.discover', compact('form'));
    }

    /**
     * Finds the AD user by the given username, and
     * generates a forgot token on success.
     *
     * @param DiscoverRequest $request
     *
     * @return bool
     */
    public function find(DiscoverRequest $request)
    {
        $profile = $this->adldap->users()->find($request->input('username'));

        if ($profile instanceof AdldapUser) {
            // Retrieve the user that has 3 or more security questions.
            $user = $this->user
                ->where('email', $profile->getEmail())
                ->has('questions', '>=', 3)
                ->first();

            // Check that we've retrieved a user from the query.
            if ($user instanceof User) {
                return $user->generateForgotToken();
            }
        }

        return false;
    }

    /**
     * Displays a form of all of the users security questions.
     *
     * @param string $token
     *
     * @return bool|\Illuminate\View\View
     */
    public function questions($token)
    {
        $user = $this->user->locateByForgotToken($token);

        if ($user instanceof User) {
            $form = $this->presenter->formQuestions($user);

            return view('pages.forgot-password.questions', compact('form'));
        }

        return false;
    }

    /**
     * Checks the users security questions against the given answers
     * and creates a reset password token which they can then
     * use to reset their AD password.
     *
     * @param QuestionRequest $request
     * @param string          $token
     *
     * @return bool|string
     */
    public function answer(QuestionRequest $request, $token)
    {
        $user = $this->user->locateByForgotToken($token);

        if ($user instanceof User) {
            // Get the submitted question answers.
            $answers = $request->input('questions', []);

            // Get the question IDs.
            $ids = array_keys($answers);

            // Try to retrieve all the users questions.
            $questions = $user->questions()->find($ids);

            // The number of correct answers.
            $correct = 0;

            // Go through each found question attached to the user.
            foreach ($questions as $question) {
                // We'll retrieve the actual answer the user gave during setup and decrypt it.
                $actual = $this->encrypter->decrypt($question->pivot->answer);

                // We'll retrieve the answer we've been given for the current question.
                $given = $answers[$question->getKey()];

                // Make sure the given answer is identical to he actual answer.
                if ($given === $actual) {
                    $correct++;
                }
            }

            // Check that the amount of correct answers equals the amount of questions found.
            if ($correct === count($questions)) {
                // If all answers are correct, we'll generate the
                // reset token for the user and return it.
                return $user->generateResetToken();
            }
        }

        return false;
    }

    /**
     * Displays the form for resetting a users password.
     *
     * @param string $token
     *
     * @return bool|\Illuminate\View\View
     */
    public function reset($token)
    {
        $user = $this->user->locateByResetToken($token);

        if ($user instanceof User) {
            $form = $this->presenter->formReset($user);

            return view('pages.forgot-password.reset', compact('form'));
        }

        return false;
    }

    /**
     * Changes the users password.
     *
     * @param PasswordRequest $request
     * @param string          $token
     *
     * @return bool
     */
    public function change(PasswordRequest $request, $token)
    {
        $user = $this->user->locateByResetToken($token);

        $profile = $this->adldap->users()->search()->where(['mail' => $user->email])->first();

        if ($user instanceof User && $profile instanceof AdldapUser) {
            $job = new ChangePassword($profile, $request->input('password'));

            $changed = $this->dispatch($job);

            if ($changed) {
                $user->clearForgotToken();
                $user->clearResetToken();
            }

            return $changed;
        }

        return false;
    }
}
