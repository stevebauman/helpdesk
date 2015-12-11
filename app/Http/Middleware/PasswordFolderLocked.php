<?php

namespace App\Http\Middleware;

use App\Models\PasswordFolder;
use Closure;
use Illuminate\Http\Request;

class PasswordFolderLocked
{
    /**
     * @var PasswordFolder
     */
    protected $folder;

    /**
     * Constructor.
     *
     * @param PasswordFolder $folder
     */
    public function __construct(PasswordFolder $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $folder = auth()->user()->load('passwordFolder')->passwordFolder;

        if ($folder instanceof PasswordFolder && $request->session()->has($folder->uuid)) {
            // Triple check here that the session UUID key contains
            // the value of the exact UUID as well
            if ($request->session()->get($folder->uuid) === $folder->uuid) {
                // User passes all checks. Allow them into the password folder.
                return $next($request);
            }
        } elseif (!$folder) {
            // If no folder exists for the user, we need
            // to redirect them to the setup page.
            return redirect()->route('passwords.setup');
        }

        // If the folder is locked and we can't see the folder
        // UUID in the users session, we'll redirect
        // them to the password gate.
        return redirect()->route('passwords.gate');
    }
}
