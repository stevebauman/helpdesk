<?php

namespace App\Models;

use App\Models\Traits\CanEncryptTrait;

class ComputerAccess extends Model
{
    use CanEncryptTrait;

    /**
     * The computer access table.
     *
     * @var string
     */
    protected $table = 'computer_access';

    /**
     * The guarded computer access attributes.
     *
     * @var array
     */
    protected $guarded = ['wmi_username', 'wmi_password'];

    /**
     * The hidden computer access attributes.
     *
     * @var array
     */
    protected $hidden = ['wmi_username', 'wmi_password'];

    /**
     * The fillable computer access attributes.
     *
     * @var array
     */
    protected $fillable = [
        'computer_id',
        'active_directory',
        'wmi',
    ];

    /**
     * Returns the encryption key.
     *
     * @return string
     */
    public function getEncryptionKey()
    {
        $key = $this->computer->getKey().config('app.key');

        return substr($key, 0, 32);
    }

    /**
     * The belongsTo computer relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function computer()
    {
        return $this->belongsTo(Computer::class, 'computer_id');
    }

    /**
     * The mutator for the notes attribute.
     *
     * @param string $username
     */
    public function setWmiUsernameAttribute($username)
    {
        $this->attributes['wmi_username'] = $this->encrypt($username);
    }

    /**
     * The mutator for the password attribute.
     *
     * @param string $password
     */
    public function setWmiPasswordAttribute($password)
    {
        $this->attributes['wmi_password'] = $this->encrypt($password);
    }

    /**
     * The accessor for the username attribute.
     *
     * @param string $username
     *
     * @return null|string
     */
    public function getWmiUsernameAttribute($username)
    {
        if (!is_null($username)) {
            return $this->decrypt($username);
        }

        return;
    }

    /**
     * The accessor for the password attribute.
     *
     * @param string $password
     *
     * @return null|string
     */
    public function getWmiPasswordAttribute($password)
    {
        if (!is_null($password)) {
            return $this->decrypt($password);
        }

        return;
    }
}
