<?php

namespace App\Models;

use App\Http\Requests\PasswordFolder\ChangePinRequest;
use App\Http\Requests\PasswordFolder\LockRequest;
use App\Http\Requests\PasswordFolder\UnlockRequest;
use App\Models\Traits\HasUserTrait;
use Illuminate\Hashing\BcryptHasher;

class PasswordFolder extends Model
{
    use HasUserTrait;

    /**
     * The password folder table.
     *
     * @var string
     */
    protected $table = 'password_folders';

    /**
     * The hidden password folder attributes.
     *
     * @var array
     */
    protected $hidden = [
        'pin',
        'uuid',
    ];

    /**
     * The guarded password folder attributes.
     *
     * @var array
     */
    protected $guarded = [
        'pin',
        'uuid',
    ];

    /**
     * The hasMany passwords relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function passwords()
    {
        return $this->hasMany(Password::class, 'folder_id');
    }

    /**
     * Mutator for the password folder PIN attribute.
     *
     * @param string $pin
     */
    public function setPinAttribute($pin)
    {
        $this->attributes['pin'] = bcrypt($pin);
    }

    /**
     * Checks the specified pin against the current password folder pin.
     *
     * @param string $pin
     *
     * @return bool
     */
    public function checkPin($pin)
    {
        $hasher = new BcryptHasher();

        return $hasher->check($pin, $this->pin);
    }

    /**
     * Changes the password folder PIN.
     *
     * @param ChangePinRequest $request
     *
     * @return bool
     */
    public function changePin(ChangePinRequest $request)
    {
        if ($this->checkPin($request->input('pin'))) {
            $this->pin = $request->input('new_pin');

            if ($this->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Unlocks a password folder by checking the specified
     * pin against the password folder pin.
     *
     * @param UnlockRequest $request
     *
     * @return bool
     */
    public function unlock(UnlockRequest $request)
    {
        if ($this->checkPin($request->input('pin'))) {
            // Store the UUID in the users session so they can have
            // access to it for as long as the session exists
            $request->session()->put($this->uuid, $this->uuid);

            return true;
        }

        return false;
    }

    /**
     * Locks the users password folder by removing it's
     * UUID from the users session.
     *
     * @param LockRequest $request
     *
     * @return bool
     */
    public function lock(LockRequest $request)
    {
        $request->session()->remove($this->uuid);

        return true;
    }
}
