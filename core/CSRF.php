<?php
namespace Caracal\Core;

class CSRF
{
    protected Session $session;

    protected string $key = '_csrf_tokens';

    protected int $length = 32;

    protected int $ttl = 1800;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function generate(): string
    {
        $token = base64_encode(random_bytes($this->length));

        $tokens = $this->session->get($this->key, []);

        $tokens[$token] = time();

        $this->session->set($this->key, $tokens);

        return $token;
    }

    public function validate(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $tokens = $this->session->get($this->key, []);

        if (!isset($tokens[$token])) {
            return false;
        }

        $created = $tokens[$token];

        unset($tokens[$token]);

        $this->session->set($this->key, $tokens);

        if ((time() - $created) > $this->ttl) {
            return false;
        }

        return true;
    }

    public function inputField(): string
    {
        $token = $this->generate();

        return '<input type="hidden" name="_csrf" value="' .
            htmlspecialchars($token, ENT_QUOTES) .
            '">';
    }

    public function token(): string
    {
        return $this->generate();
    }

    public function checkPost(): bool
    {
        $token = $_POST['_csrf'] ?? null;

        return $this->validate($token);
    }

    public function checkHeader(): bool
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        return $this->validate($token);
    }

    public function clear(): void
    {
        $this->session->remove($this->key);
    }
}