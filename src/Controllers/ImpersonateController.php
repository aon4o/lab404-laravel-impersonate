<?php

namespace Lab404\Impersonate\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Lab404\Impersonate\Exceptions\InvalidUserProvider;
use Lab404\Impersonate\Exceptions\MissingUserProvider;
use Lab404\Impersonate\Services\ImpersonateManager;

class ImpersonateController extends Controller
{
    protected ImpersonateManager $manager;

    /**
     * ImpersonateController constructor.
     */
    public function __construct()
    {
        $this->manager = app()->make(ImpersonateManager::class);
        
        $guard = $this->manager->getDefaultSessionGuard();
        $this->middleware('auth:' . $guard)->only('take');
    }

    /**
     * @param  Request  $request
     * @param  int  $id
     * @param  string|null  $guardName
     *
     * @return  RedirectResponse
     *
     * @throws InvalidUserProvider
     * @throws MissingUserProvider
     */
    public function take(Request $request, int $id, string|null $guardName = null): RedirectResponse
    {
        $guardName = $guardName ?? $this->manager->getDefaultSessionGuard();

        // Cannot impersonate yourself
        if ($id == $request->user()->getAuthIdentifier() && ($this->manager->getCurrentAuthGuardName() == $guardName)) {
            abort(403);
        }

        // Cannot impersonate again if you're already impersonating a user
        if ($this->manager->isImpersonating()) {
            abort(403);
        }

        if (!$request->user()->canImpersonate()) {
            abort(403);
        }

        $userToImpersonate = $this->manager->findUserById($id, $guardName);

        if ($userToImpersonate->canBeImpersonated() &&
            $this->manager->take($request->user(), $userToImpersonate, $guardName)
        ) {
            $takeRedirect = $this->manager->getTakeRedirectTo();

            if ($takeRedirect !== 'back') {
                return redirect()->to($takeRedirect);
            }
        }

        return redirect()->back();
    }

    /**
     * @return RedirectResponse
     */
    public function leave(): RedirectResponse
    {
        if (!$this->manager->isImpersonating()) {
            abort(403);
        }

        $this->manager->leave();

        $leaveRedirect = $this->manager->getLeaveRedirectTo();

        if ($leaveRedirect !== 'back') {
            return redirect()->to($leaveRedirect);
        }

        return redirect()->back();
    }
}
