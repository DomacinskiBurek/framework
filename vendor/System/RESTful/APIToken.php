<?php

namespace DomacinskiBurek\System\RESTful;

use DomacinskiBurek\System\Database;
use DomacinskiBurek\System\Error\Handlers\DatabaseException;
use DomacinskiBurek\System\Query;
use DomainException;
use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use InvalidArgumentException;
use Firebase\JWT\JWT;
use PDOException;
use Ramsey\Uuid\Uuid;
use UnexpectedValueException;

class APIToken
{
    private string $algorithm = "HS256";
    private int $period       = 86300;
    private int $leeway       = 60;

    private int $createdAt;
    private int $notBefore;
    private int $expiringAt;

    private string $publicIssuer = "domacinskiburek_tokens";
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

    public function setAudience (string $origin): self
    {
        $this->publicAudience = $origin;

        return $this;
    }

    public function setLeeway (): self
    {
        JWT::$leeway = $this->leeway;

        return $this;
    }

    /**
     * @throws DatabaseException
     */
    public function createToken ()
    {
        $database = Database::connect();

        $tokenPrivateKey = uuid::uuid4();
        $tokenPublicKey  = JWT::encode([
            "iss" => $this->publicIssuer,
            "aud" => $this->publicAudience,
            "iat" => $this->createdAt,
            "nbf" => $this->notBefore,
            "exp" => $this->expiringAt
        ], $tokenPrivateKey, $this->algorithm);

        try {
            $database->prepare(Query::generate("Insert::create_api_access_token"))->execute([$tokenPublicKey, $tokenPrivateKey, 1]);
        } catch (PDOException|Exception $e) {
            return $e->getMessage();
        }

        return $tokenPublicKey;
    }
}