<?php

namespace Lab404\Impersonate\Guard;

use Illuminate\Auth\SessionGuard as BaseSessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;

class SessionGuard extends BaseSessionGuard
{
    /**
     * Log a user into the application without firing the Login event.
     *
     * @param  Authenticatable  $user
     * @return void
     */
    public function quietLogin(Authenticatable $user): void
    {
        $this->updateSession($user->getAuthIdentifier());

        $this->setUser($user);
    }

    /**
     * Logout the user without updating remember_token
     * and without firing the Logout event.
     *
     * @return  void
     */
    public function quietLogout(): void
    {
        $this->clearUserDataFromStorage();

        $this->user = null;

        $this->loggedOut = true;
    }
}
