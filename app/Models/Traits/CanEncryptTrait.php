<?php

namespace App\Models\Traits;

use Illuminate\Encryption\Encrypter;

trait CanEncryptTrait
{
    /**
     * Returns the encryption key.
     *
     * @return string
     */
    abstract public function getEncryptionKey();

    /**
     * Encrypts a string.
     *
     * @param string $string
     *
     * @return string
     */
    protected function encrypt($string)
    {
        return $this->newEncrypter()->encrypt($string);
    }

    /**
     * Decrypts a string.
     *
     * @param string $string
     *
     * @return string
     */
    protected function decrypt($string)
    {
        return $this->newEncrypter()->decrypt($string);
    }

    /**
     * Returns a new encrypter instance.
     *
     * @param string $key
     *
     * @return Encrypter
     */
    protected function newEncrypter($key = null)
    {
        if (is_null($key)) {
            $key = $this->getEncryptionKey();
        }

        return new Encrypter($key, 'AES-256-CBC');
    }
}
