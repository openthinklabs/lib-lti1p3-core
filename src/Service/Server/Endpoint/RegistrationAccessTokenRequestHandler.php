<?php

/**
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; under version 2
 *  of the License (non-upgradable).
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *  Copyright (c) 2020 (original work) Open Assessment Technologies S.A.
 */

declare(strict_types=1);

namespace OAT\Library\Lti1p3Core\Service\Server\Endpoint;

use League\OAuth2\Server\Exception\OAuthServerException;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Service\Server\Factory\AuthorizationServerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RegistrationAccessTokenRequestHandler
{
    /** @var RegistrationRepositoryInterface */
    private $repository;

    /** @var AuthorizationServerFactory */
    private $factory;

    public function __construct(RegistrationRepositoryInterface $repository, AuthorizationServerFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @throws OAuthServerException
     */
    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $registrationIdentifier
    ): ResponseInterface {
        $registration = $this->repository->find($registrationIdentifier);

        if (null === $registration) {
            throw new OAuthServerException('Invalid registration identifier', 11, 'registration_not_found', 404);
        }

        return $this->factory
            ->createForRegistration($registration)
            ->respondToAccessTokenRequest($request, $response);
    }
}
