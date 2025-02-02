<?php

namespace Lab404\Impersonate\Models;

use Illuminate\Database\Eloquent\Model;
use Lab404\Impersonate\Services\ImpersonateManager;

trait Impersonate
{
    /**
     * Return true or false if the user can impersonate another user.
     *
     * @return  bool
     */
    public function canImpersonate(): bool
    {
        return true;
    }

    /**
     * Return true or false if the user can be impersonated.
     *
     * @return  bool
     */
    public function canBeImpersonated(): bool
    {
        return true;
    }

    /**
     * Impersonate the given user.
     *
     * @param Model       $user
     * @param  string|null  $guardName
     *
     * @return  bool
     */
    public function impersonate(Model $user, ?string $guardName = null): bool
    {
        return app(ImpersonateManager::class)->take($this, $user, $guardName);
    }

    /**
     * Check if the current user is impersonated.
     *
     * @return  bool
     */
    public function isImpersonated(): bool
    {
        return app(ImpersonateManager::class)->isImpersonating();
    }

    /**
     * Leave the current impersonation.
     *
     * @return  bool
     */
    public function leaveImpersonation(): bool
    {
        if ($this->isImpersonated()) {
            return app(ImpersonateManager::class)->leave();
        }

        return false;
    }
}
