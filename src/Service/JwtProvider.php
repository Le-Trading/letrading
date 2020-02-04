<?php

namespace App\Service;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class JwtProvider
{
    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function __invoke(): string
    {
        return (new Builder())
            ->set('mercure', ['publish' => ['*']])
            ->sign(new Sha256(), $this->secret)
            ->getToken();
    }
}