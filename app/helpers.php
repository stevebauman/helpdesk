<?php

use Rhumsaa\Uuid\Uuid;

/**
 * Generates a session flash message.
 *
 * @param null|string $title
 * @param null|string $message
 *
 * @return null|\App\Http\Flash
 */
function flash($title = null, $message = null)
{
    $flash = new \App\Http\Flash();

    if (func_num_args() === 0) {
        return $flash;
    }

    $flash->info($title, $message);
}

/**
 * Generates a unique UUID string.
 *
 * @return string
 */
function uuid()
{
    return Uuid::uuid3(Uuid::NAMESPACE_DNS, str_random())->toString();
}

/**
 * Generates a new Active instance.
 *
 * @return \App\Http\Active
 */
function active()
{
    return new \App\Http\Active();
}

/**
 * Validates that the inserted string is an object SID.
 *
 * @param string $sid
 *
 * @return bool
 */
function isValidSid($sid)
{
    preg_match("/S-1-5-21-\d+-\d+\-\d+\-\d+/", $sid, $matches);

    if (count($matches) > 0) {
        return true;
    }

    return false;
}
