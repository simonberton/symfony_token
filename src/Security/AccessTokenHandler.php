<?php

namespace App\Security;

use App\Repository\AccessTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private AccessTokenRepository $repository
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        // e.g. query the "access token" database to search for this token
        $token = $this->repository->findOneByValue($accessToken);
        if (null === $token || !$token->isValid()) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        // Return a UserBadge with the user identifier (e.g., name, email, or id)
        return new UserBadge($token->getUserIdentifier()); // e.g. ->
    }
}
