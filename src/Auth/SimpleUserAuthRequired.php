<?php

namespace Readdle\QuickBike\Auth;

use Readdle\Database\FQDB;
use Readdle\QuickBike\Auth\Exception\AuthRedirectException;
use Readdle\QuickBike\Config\GeneratedRuntimeConfig;
use Symfony\Component\HttpFoundation\Session\Session;

class SimpleUserAuthRequired extends SimpleUserAuth
{
    public readonly string $user;

    /**
     * @throws AuthRedirectException
     */
    public function __construct(
        protected Session $session,
        FQDB $fqdb
    ) {
        parent::__construct($session, $fqdb);

        if (!$this->isLoggedIn()) {
            throw new AuthRedirectException();
        }

        $this->user = $this->getAuthenticatedUser();
    }

    public function getUserConfigKey(string $key) : mixed
    {
        if (!array_key_exists($key, GeneratedRuntimeConfig::USERS[$this->user])) {
            return null;
        }

        return GeneratedRuntimeConfig::USERS[$this->user][$key];
    }
}
