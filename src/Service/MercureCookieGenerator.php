<?php

namespace App\Service;


use App\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha384;

class MercureCookieGenerator
{

    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function generate(User $user){
        $token = (new Builder())
            ->set('mercure', ['subscribe' => ["http://monsite.com/user/{$user->getId()}"]])
            ->sign(new Sha384(), $this->secret)
            ->getToken();
        return "mercureAuthorization={$token}; Path=/hub; HttpOnly;";
    }
}