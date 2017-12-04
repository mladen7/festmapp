<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/14/2017
 * Time: 2:08 PM
 */

namespace Security\JWT;

use Exceptions\TokenClaimMissingException;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Lcobucci\JWT;

class JsonWebToken
{
    private static $previousKey;
    private $currentKey = 'TCat_PS_IT_13413';
    private $parser;
    private $logger;

    /**
     * JsonWebToken constructor.
     */
    public function __construct()
    {
        $this->parser = new JWT\Parser();
        $this->logger = new FileAdapter(APP_PATH.'/logs/token-validation.log');
    }

    public function buildTokenWithUserID($user_id)
    {
        $signer = new JWT\Signer\Hmac\Sha256();
        $token = (new JWT\Builder())->setIssuer('http://festivali-api.com')// Configures the issuer (iss claim)
        ->setAudience('festivali-user')// Configures the audience (aud claim)
        ->setId('4f1g23a12aa')// Configures the id (jti claim), replicating as a header item
        ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
        ->setNotBefore(time())// Configures the time that the token can be used (nbf claim)
        ->setExpiration(time() + 120 * 20)// Configures the expiration time of the token (exp claim)
        ->set('user_id', $user_id);

        //creates JWT
        $token = $token->sign($signer, $this->currentKey)->getToken();

        return $token->__toString(); // Retrieves the generated token
    }

    /**
     * verifies token + checks if it is expired
     * @param $token
     * @return bool
     */
    public function fullTokenVerification($token)
    {
        return $this->verifyToken($token) and !$this->isExpired($token);
    }

    /**
     * Verifies token
     * @param $token
     * @return bool
     */
    public function verifyToken($token)
    {
        try {
            $signer = new JWT\Signer\Hmac\Sha256();
            $token = $this->parser->parse($token);
            return $token->verify($signer, $this->currentKey);
        } catch (\Exception $e) {
            $this->logger->log("[Token ERROR] " . $e->getMessage(), Logger::ERROR);
            return false;
        }
    }

    /**
     * Check if token string parameter is expired
     * @param $token -string
     * @return bool
     */
    public function isExpired($token)
    {
        try {
            $token = $this->parser->parse($token);
            return $token->isExpired();
        } catch (\Exception $e) {
            $this->logger->log("[Token ERROR] " . $e->getMessage(), Logger::ERROR);
            return false;
        }
    }

    /**
     * Get user id from token string
     * @param $token
     * @return mixed
     */
    public function getUserId($token)
    {
        try {
            $token = $this->parser->parse($token);
            return $token->getClaim('user_id');
        } catch (\Exception $e) {
            $this->logger->log("[Token ERROR] " . $e->getMessage(), Logger::ERROR);
            return new TokenClaimMissingException('User ID');
        }
    }

    /**
     * Tries to get claim from token
     * @param $token
     * @param $claim
     * @return TokenClaimMissingException|mixed
     */
    public function getClam($token, $claim)
    {
        try {
            $token = $this->parser->parse($token);
            return $token->getClaim($claim);
        } catch (\Exception $e) {
            $this->logger->log("[Token ERROR] " . $e->getMessage(), Logger::ERROR);
            return new TokenClaimMissingException($claim);
        }
    }

    //    public function validateToken($token)
//    {
//        $data = new JWT\ValidationData(); // It will use the current time to validate (iat, nbf and exp)
//        $data->setIssuer('http://TCat.com');
//        $data->setAudience('user');
//        $data->setId('4f1g23a12aa');
//        $data->setCurrentTime(time());
//        $signer = new JWT\Signer\Hmac\Sha256();
//        $parser = new JWT\Parser();
//        $token = $parser->parse($token);
//        return $token->verify($signer, $this->currentKey);
//    }

}