<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Class Auth
 * @package Source\Models
 */
class Auth extends Model
{
    /**
     * Auth constructor.
     */
    public function __construct()
    {
        parent::__construct("users", ["id"], ["email", "password"]);
    }

    /**
     * @param string $email
     * @param string $password
     * @param int $level
     * @return User|null
     */
    public function attempt(string $email, string $password): ?User
    {
        if (!is_email($email)) {
            $this->message->warning("O e-mail informado não é válido");
            return null;
        }

        if (!is_passwd($password)) {
            $this->message->warning("A senha informada não é válida");
            return null;
        }

        $user = (new User())->findByEmail($email);

        if (!$user) {
            $this->message->error("O e-mail informado não está cadastrado");
            return null;
        }

        if (!passwd_verify($password, $user->password)) {
            $this->message->error("A senha informada não confere");
            return null;
        }

        if (passwd_rehash($user->password)) {
            $user->password = $password;
            $user->save();
        }

        return $user;
    }
}