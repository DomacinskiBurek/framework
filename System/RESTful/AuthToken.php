<?php

namespace DomacinskiBurek\System\RESTful;

use DomainException;
use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use InvalidArgumentException;
use Firebase\JWT\JWT;
use UnexpectedValueException;

class AuthToken
{
    private string $algorithm = "HS256";
    private int $period       = 86300;
    private int $leeway       = 60;

    private int $createdAt;
    private int $notBefore;
    private int $expiringAt;

    private string $publicIssuer;
    private string $publicAudience;

    public function setValidPeriod (int $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function setCreatedTime (int $time): self
    {
        $this->createdAt = $time;

        return $this;
    }

    public function setNotBefore (int $time, int $seconds = 0): self
    {
        $this->notBefore = $seconds == 0 ? $this->createdAt : $time + $seconds;

        return $this;
    }

    public function setValidUntil (int $time): self
    {
        $this->expiringAt = $time + $this->period;

        return $this;
    }

    public function setIssuer (string $issuer): self
    {
        $this->publicIssuer = $issuer;

        return $this;
    }

    public function setAudience (int $client_id): self
    {
        $this->publicAudience = $client_id;

        return $this;
    }

    public function setLeeway (): self
    {
        JWT::$leeway = $this->leeway;

        return $this;
    }

    public function encryptToken (string $key): string
    {
        return JWT::encode(
            [
                "iss" => $this->publicIssuer,
                "aud" => $this->publicAudience,
                "iat" => $this->createdAt,
                "nbf" => $this->notBefore,
                "exp" => $this->expiringAt
            ],
            $key,
            $this->algorithm
        );
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws DomainException
     * @throws SignatureInvalidException
     * @throws BeforeValidException
     * @throws ExpiredException
     * @throws UnexpectedValueException
     */
    public function decryptToken (string $key, string $token): array
    {
        $tokenBody = JWT::decode($token, new Key($key, $this->algorithm));

        return [
            "publicAudience" => $tokenBody->aud,
            "publicIssuer"   => $tokenBody->iss,
            "createdAt"      => $tokenBody->iat,
            "notBefore"      => $tokenBody->nbf,
            "expiringAt"     => $tokenBody->exp
        ];
    }

    public function isTokenValid (string $key, string $token): bool
    {
        try {
            JWT::decode($token, new Key($key, $this->algorithm));
        } catch (SignatureInvalidException $error) {
            return false;
        }

        return true;
    }

    public static function getUniqueKey (): string
    {
        return md5(time() . microtime(true) . rand(0, 999999));
    }
}