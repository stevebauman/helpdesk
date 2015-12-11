<?php

namespace App\Processors\ActiveDirectory;

use Adldap\Contracts\Adldap;
use Adldap\Models\User;
use Adldap\Objects\AccountControl;
use Adldap\Schemas\ActiveDirectory;
use App\Http\Presenters\ActiveDirectory\UserPresenter;
use App\Http\Requests\ActiveDirectory\UserImportRequest;
use App\Http\Requests\ActiveDirectory\UserRequest;
use App\Jobs\ActiveDirectory\ImportUser;
use App\Processors\Processor;
use Illuminate\Http\Request;

class UserProcessor extends Processor
{
    /**
     * @var UserPresenter
     */
    protected $presenter;

    /**
     * @var Adldap
     */
    protected $adldap;

    /**
     * Constructor.
     *
     * @param UserPresenter $presenter
     * @param Adldap        $adldap
     */
    public function __construct(UserPresenter $presenter, Adldap $adldap)
    {
        $this->presenter = $presenter;
        $this->adldap = $adldap;
    }

    /**
     * Displays all active directory users.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('index', User::class);

        $search = $this->adldap->users()->search();

        if ($request->has('q')) {
            $query = $request->input('q');

            $search = $search
                ->orWhereContains(ActiveDirectory::COMMON_NAME, $query)
                ->orWhereContains(ActiveDirectory::DESCRIPTION, $query)
                ->orWhereContains(ActiveDirectory::OPERATING_SYSTEM, $query);
        }

        $all = $search
            ->whereHas(ActiveDirectory::EMAIL)
            ->sortBy(ActiveDirectory::COMMON_NAME, 'asc')->get();

        $users = $this->presenter->table($all->toArray());

        $navbar = $this->presenter->navbar();

        return view('pages.active-directory.users.index', compact('users', 'navbar'));
    }

    public function create()
    {
        $user = $this->adldap->users()->newInstance();

        $form = $this->presenter->form($user);

        return view('pages.active-directory.users.create', compact('form'));
    }

    /**
     * Creates a new active directory user.
     *
     * @param UserRequest $request
     *
     * @return bool
     */
    public function store(UserRequest $request)
    {
        $user = $this->adldap->users()->newInstance();

        $user->setAccountName($request->input('username'));
        $user->setEmail($request->input('email'));
        $user->setFirstName($request->input('first_name'));
        $user->setLastName($request->input('last_name'));
        $user->setDisplayName($request->input('display_name'));
        $user->setDescription($request->input('description'));
        $user->setProfilePath($request->input('profile_path'));
        $user->setScriptPath($request->input('logon_script'));

        $ac = $this->createUserAccountControl($request, $user);

        $user->setUserAccountControl($ac);

        return $user->save();
    }

    /**
     * Displays the information page for the specified user.
     *
     * @param string $username
     *
     * @return \Illuminate\View\View
     */
    public function show($username)
    {
        $user = $this->adldap->users()->find($username);

        if ($user instanceof User) {
            return view('pages.active-directory.users.show', compact('user'));
        }

        abort(404);
    }

    /**
     * Displays the form for editing the specified active directory user.
     *
     * @param string $username
     *
     * @return \Illuminate\View\View
     */
    public function edit($username)
    {
        $user = $this->adldap->users()->find($username);

        if ($user instanceof User) {
            $form = $this->presenter->form($user);

            return view('pages.active-directory.users.edit', compact('form'));
        }

        abort(404);
    }

    /**
     * Updates the specified active directory user.
     *
     * @param UserRequest $request
     * @param string      $username
     *
     * @return bool
     */
    public function update(UserRequest $request, $username)
    {
        $user = $this->adldap->users()->find($username);

        if ($user instanceof User) {
            $user->setAccountName($request->input('username', $user->getAccountName()));
            $user->setEmail($request->input('email', $user->getEmail()));
            $user->setFirstName($request->input('first_name', $user->getFirstName()));
            $user->setLastName($request->input('last_name', $user->getLastName()));
            $user->setDisplayName($request->input('display_name', $user->getDisplayName()));
            $user->setDescription($request->input('description', $user->getDescription()));
            $user->setProfilePath($request->input('profile_path', $user->getProfilePath()));
            $user->setScriptPath($request->input('logon_script', $user->getScriptPath()));

            $ac = $this->createUserAccountControl($request, $user);

            $user->setUserAccountControl($ac);

            return $user->save();
        }

        abort(404);
    }

    /**
     * Imports an active directory user.
     *
     * @param UserImportRequest $request
     *
     * @return bool|mixed
     */
    public function import(UserImportRequest $request)
    {
        $this->authorize('store', User::class);

        $user = $this->adldap->search()->findByDn($request->input('dn'));

        if ($user instanceof User) {
            return $this->dispatch(new ImportUser($user));
        }

        return false;
    }

    /**
     * Creates an account control object by the specified requests parameters.
     *
     * @param UserRequest $request
     * @param User        $user
     *
     * @return AccountControl
     */
    protected function createUserAccountControl(UserRequest $request, User $user)
    {
        $ac = new AccountControl($user->getUserAccountControl());

        if ($request->has('control_normal_account')) {
            $ac->accountIsNormal();
        }

        if ($request->has('control_password_is_expired')) {
            $ac->passwordIsExpired();
        }

        if ($request->has('control_password_does_not_expire')) {
            $ac->passwordDoesNotExpire();
        }

        if ($request->has('control_locked')) {
            $ac->accountIsLocked();
        }

        if ($request->has('control_disabled')) {
            $ac->accountIsDisabled();
        }

        if ($request->has('control_smartcard_required')) {
            $ac->accountRequiresSmartCard();
        }

        return $ac;
    }
}
