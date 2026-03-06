<?php
namespace Caracal\Core;

class CSRF
{
    protected Session $session;
    protected string $key = '_csrf_token';
    protected int $length = 32;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function generate(): string
    {
        $appKey = Helpers::env('APP_KEY');
        $random = random_bytes($this->length);
        $token = hash_hmac('sha256', $random, $appKey);

        $this->session->set($this->key, $token);

        return $token;
    }

    public function validate(?string $token): bool
    {
        if (!$token) return false;

        $stored = $this->session->get($this->key);
        if (!$stored) return false;

        $this->session->remove($this->key);

        return hash_equals($stored, $token);
    }

    public function inputField(): string
    {
        $token = $this->generate();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
    }

    public function checkPost(): bool
    {
        $token = $_POST['_csrf'] ?? null;
        return $this->validate($token);
    }
}
