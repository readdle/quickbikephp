<?php

namespace Readdle\QuickBike\Auth;

use Readdle\Database\FQDB;
use Readdle\QuickBike\Config\GeneratedRuntimeConfig;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * This class excepts GeneratedRuntimeConfig::USERS array
 * key is a username
 * value is an array, various key-value parts
 * but important one is pwd — php password_hash of a password
 * example
 * USERS => [ 'admin' => [ 'pwd' => '... hash ...' ] ]
 */

class SimpleUserAuth
{
    protected const RATE_LIMIT_OLD = 7 * 86400;
    protected const RATE_LIMIT_SECONDS = 6;
    protected const USERNAME_KEY = 'user';
    protected const PWHASH_KEY = 'pwhash';

    public function __construct(
        protected Session $session,
        protected FQDB $fqdb
    ) {
    }

    protected function putAuthErrorToSessionFlash(): void
    {
        $this->session->getFlashBag()->set('error', 'unknown user or wrong password');
    }

    protected function putRateLimitErrorToSessionFlash(): void
    {
        $this->session->getFlashBag()->set('error', 'too many tries, wait a little bit');
    }


    protected function garbageCollect(): void
    {
        $this->fqdb->delete(
            'DELETE FROM rate_limit WHERE hit_time < :hit_time_old',
            [
                ':hit_time_old' => time() - self::RATE_LIMIT_OLD
            ]
        );
    }

    protected function rateLimitCheck(string $username, string $ip): bool
    {
        $this->garbageCollect();

        $params = [
            ':ip'       => $ip,
            ':username' => $username
        ];

        $paramsCheck = $params;
        $paramsCheck[':hit_time_min'] = time() - self::RATE_LIMIT_SECONDS;

        $cnt = $this->fqdb->queryValue('SELECT COUNT(*) 
                                                FROM rate_limit 
                                               WHERE ip=:ip AND context=:username 
                                                     AND hit_time >  :hit_time_min', $paramsCheck);

        $paramsInsert = $params;
        $paramsInsert[':hit_time'] = time();

        $this->fqdb->insert(
            'INSERT INTO rate_limit(ip, context, hit_time) 
                                        VALUES (:ip, :username, :hit_time)',
            $paramsInsert
        );

        return intval($cnt) == 0;
    }

    public function checkCredentialsAndUpdateSession(string $username, string $password, string $ip): bool
    {
        if (!array_key_exists($username, GeneratedRuntimeConfig::USERS)) {
            $this->putAuthErrorToSessionFlash();
            return false;
        }
        $goldHash = GeneratedRuntimeConfig::USERS[$username]['pwd'];

        if (!$this->rateLimitCheck($username, $ip)) {
            $this->putRateLimitErrorToSessionFlash();
            return false;
        }

        if (password_verify($password, $goldHash)) {
            $this->session->set(self::USERNAME_KEY, $username);
            $this->session->set(self::PWHASH_KEY, $goldHash);
            return true;
        } else {
            $this->putAuthErrorToSessionFlash();
            return false;
        }
    }

    public function getAuthenticatedUser(): ?string
    {
        $user = $this->session->get(self::USERNAME_KEY);
        $storedHash = $this->session->get(self::PWHASH_KEY);

        return (array_key_exists($user, GeneratedRuntimeConfig::USERS) &&
                    GeneratedRuntimeConfig::USERS[$user]['pwd'] === $storedHash) ? $user : null;
    }

    public function isLoggedIn(): bool
    {
        return $this->getAuthenticatedUser() !== null;
    }

    public function redirectIfNotLogged(): ?RedirectResponse
    {
        if (!$this->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        return null;
    }
}
