<?php

namespace App\Http\Middleware;

use App\Models\PasswordFolder;
use Closure;
use Illuminate\Http\Request;

class PasswordFolderSetup
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
        $folder = $this->folder->where('user_id', auth()->user()->getKey())->first();

        if ($folder instanceof PasswordFolder) {
            // If a folder already exists, the user is trying to access
            // setup again. We'll redirect them to an 'invalid' page.
            return redirect()->route('passwords.setup.invalid');
        } else {
            // Looks like the user doesn't have a password folder
            // yet. We'll let them proceed into setup.
            return $next($request);
        }
    }
}
