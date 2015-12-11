<?php

namespace App\Models;

use App\Models\Traits\CanEncryptTrait;

class Password extends Model
{
    use CanEncryptTrait;

    /**
     * The passwords table.
     *
     * @var string
     */
    protected $table = 'passwords';

    /**
     * The hidden password attributes.
     *
     * @var array
     */
    protected $hidden = ['username', 'website', 'password', 'notes'];

    /**
     * The guarded password attributes.
     *
     * @var array
     */
    protected $guarded = ['username', 'website', 'password', 'notes'];

    /**
     * The belongsTo folder relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo(PasswordFolder::class, 'folder_id');
    }

    /**
     * Returns the encryption key.
     *
     * @return string
     */
    public function getEncryptionKey()
    {
        $folder = $this->folder;

        $user = $folder->user;

        $key = $user->getKey().config('app.key').$folder->pin;

        return substr($key, 0, 32);
    }

    /**
     * The mutator for the title attribute.
     *
     * @param string $title
     */
    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = $this->encrypt($title);
    }

    /**
     * The mutator for the website attribute.
     *
     * @param string $website
     */
    public function setWebsiteAttribute($website)
    {
        $this->attributes['website'] = $this->encrypt($website);
    }

    /**
     * The mutator for the username attribute.
     *
     * @param string $username
     */
    public function setUsernameAttribute($username)
    {
        $this->attributes['username'] = $this->encrypt($username);
    }

    /**
     * The mutator for the password attribute.
     *
     * @param string $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = $this->encrypt($password);
    }

    /**
     * The mutator for the notes attribute.
     *
     * @param string $notes
     */
    public function setNotesAttribute($notes)
    {
        $this->attributes['notes'] = $this->encrypt($notes);
    }

    /**
     * The accessor for the title attribute.
     *
     * @param string $title
     *
     * @return null|string
     */
    public function getTitleAttribute($title)
    {
        if (!is_null($title)) {
            return $this->decrypt($title);
        }

        return;
    }

    /**
     * The accessor for the website attribute.
     *
     * @param string $website
     *
     * @return null|string
     */
    public function getWebsiteAttribute($website)
    {
        if (!is_null($website)) {
            return $this->decrypt($website);
        }

        return;
    }

    /**
     * The accessor for the username attribute.
     *
     * @param string $username
     *
     * @return null|string
     */
    public function getUsernameAttribute($username)
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
    public function getPasswordAttribute($password)
    {
        if (!is_null($password)) {
            return $this->decrypt($password);
        }

        return;
    }

    /**
     * The accessor for the notes attribute.
     *
     * @param string $notes
     *
     * @return null|string
     */
    public function getNotesAttribute($notes)
    {
        if (!is_null($notes)) {
            return $this->decrypt($notes);
        }

        return;
    }
}
