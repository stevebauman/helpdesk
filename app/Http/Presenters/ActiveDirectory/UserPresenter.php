<?php

namespace App\Http\Presenters\ActiveDirectory;

use Adldap\Models\User as AdUser;
use Adldap\Objects\AccountControl;
use App\Http\Presenters\Presenter;
use App\Models\User;
use Orchestra\Contracts\Html\Form\Fieldset;
use Orchestra\Contracts\Html\Form\Grid as FormGrid;
use Orchestra\Contracts\Html\Table\Grid as TableGrid;
use Orchestra\Support\Facades\HTML;

class UserPresenter extends Presenter
{
    /**
     * Returns a new table of all active directory users.
     *
     * @param array $users
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function table(array $users = [])
    {
        return $this->table->of('active-directory.users', function (TableGrid $table) use ($users) {
            $table->attributes('class', 'table table-hover');

            $table->rows($users);

            $table->column('name', function ($column) {
                $column->label = 'Name';
                $column->value = function (AdUser $user) {
                    return link_to_route('active-directory.users.edit', $user->getName(), [$user->getAccountName()]);
                };
            });

            $table->column('email', function ($column) {
                $column->label = 'Email';
                $column->value = function (AdUser $user) {
                    return $user->getEmail();
                };
            });

            $table->column('department', function ($column) {
                $column->label = 'Department';
                $column->value = function (AdUser $user) {
                    return $user->getDepartment();
                };
            });

            $table->column('add', function ($column) {
                $column->attributes(function () {
                    return ['class' => 'text-center'];
                });

                $column->label = 'Add';
                $column->value = function (AdUser $user) {
                    $exists = User::where('email', $user->getEmail())->first();

                    if ($exists) {
                        return $this->formAdded();
                    } else {
                        return $this->formAdd($user);
                    }
                };
            });
        });
    }

    /**
     * Returns a new form for the specified active directory user.
     *
     * @param AdUser $user
     *
     * @return \Orchestra\Contracts\Html\Builder
     */
    public function form(AdUser $user)
    {
        return $this->form->of('active-directory.users', function (FormGrid $form) use ($user) {
            if ($user->exists) {
                $url = route('active-directory.users.update', [$user->getAccountName()]);
                $method = 'PATCH';
                $form->submit = 'Save';
            } else {
                $url = route('active-directory.users.store');
                $method = 'POST';
                $form->submit = 'Create';
            }

            $form->attributes(compact('url', 'method'));

            $form->fieldset(function (Fieldset $fieldset) use ($user) {
                $fieldset
                    ->control('input:text', 'username')
                    ->value($user->getAccountName())
                    ->attributes(['placeholder' => 'The users SAMAccountName']);

                $fieldset
                    ->control('input:text', 'email')
                    ->value($user->getEmail())
                    ->attributes(['placeholder' => 'The users email']);

                $fieldset
                    ->control('input:text', 'first_name')
                    ->value($user->getFirstName())
                    ->attributes(['placeholder' => 'The users first name']);

                $fieldset
                    ->control('input:text', 'last_name')
                    ->value($user->getLastName())
                    ->attributes(['placeholder' => 'The users last name']);

                $fieldset
                    ->control('input:text', 'display_name')
                    ->value($user->getDisplayName())
                    ->attributes(['placeholder' => 'The users display name']);

                $fieldset
                    ->control('input:text', 'description')
                    ->value($user->getDescription())
                    ->attributes(['placeholder' => 'The users description']);

                $fieldset
                    ->control('input:text', 'profile_path')
                    ->value($user->getProfilePath())
                    ->attributes(['placeholder' => 'The users profile path']);
            });

            $form->fieldset('User Account Control', function (Fieldset $fieldset) use ($user) {
                $ac = new AccountControl($user->getUserAccountControl());

                $values = $ac->getValues();

                $fieldset
                    ->control('input:checkbox', 'control_normal_account')
                    ->label('User account is normal')
                    ->value('1')
                    ->checked(in_array(AccountControl::NORMAL_ACCOUNT, $values))
                    ->attributes(['class' => 'switch-mark']);

                $fieldset
                    ->control('input:checkbox', 'control_password_is_expired')
                    ->label('User must change password on next logon')
                    ->value('1')
                    ->checked(in_array(AccountControl::PASSWORD_EXPIRED, $values))
                    ->attributes(['class' => 'switch-mark']);

                $fieldset
                    ->control('input:checkbox', 'control_password_does_not_expire')
                    ->label('User password does not expire')
                    ->value('1')
                    ->checked(in_array(AccountControl::DONT_EXPIRE_PASSWORD, $values))
                    ->attributes(['class' => 'switch-mark']);

                $fieldset
                    ->control('input:checkbox', 'control_locked')
                    ->label('Users account is locked')
                    ->value('1')
                    ->checked(in_array(AccountControl::LOCKOUT, $values))
                    ->attributes(['class' => 'switch-mark']);

                $fieldset
                    ->control('input:checkbox', 'control_disabled')
                    ->label('Users account is disabled')
                    ->value('1')
                    ->checked(in_array(AccountControl::ACCOUNTDISABLE, $values))
                    ->attributes(['class' => 'switch-mark']);

                $fieldset
                    ->control('input:checkbox', 'control_smartcard_required')
                    ->label('Smart card is required for interactive logon')
                    ->value('1')
                    ->checked(in_array(AccountControl::SMARTCARD_REQUIRED, $values))
                    ->attributes(['class' => 'switch-mark']);
            });
        });
    }

    /**
     * Returns a form for adding an AD computer.
     *
     * @param AdUser $user
     *
     * @return object|\Orchestra\Contracts\Html\Builder
     */
    public function formAdd(AdUser $user)
    {
        $key = sprintf('active-directory.users.%s', $user->getAccountName());

        return $this->form->of($key, function (FormGrid $form) use ($user) {
            $form->attributes([
                'url'    => route('active-directory.users.import'),
                'method' => 'POST',
            ]);

            $form->hidden('dn', function ($field) use ($user) {
                $field->value = $user->getDn();
            });

            $form->submit = 'Add';
        });
    }

    /**
     * Returns a button displaying that the user has already been added.
     *
     * @return string
     */
    public function formAdded()
    {
        return HTML::create('input', null, [
            'type'     => 'submit',
            'class'    => 'btn btn-primary',
            'value'    => 'Added',
            'disabled' => true,
        ]);
    }

    /**
     * Returns a new navbar for the active directory users index.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function navbar()
    {
        return $this->fluent([
            'id'         => 'ad-users',
            'title'      => 'Users',
            'url'        => route('active-directory.users.index'),
            'menu'       => view('pages.active-directory.users._nav'),
            'attributes' => [
                'class' => 'navbar-default',
            ],
        ]);
    }
}
