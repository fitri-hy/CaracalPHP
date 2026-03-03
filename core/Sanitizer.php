<?php
namespace Caracal\Core;

use HTMLPurifier;
use HTMLPurifier_Config;

class Sanitizer
{
    protected HTMLPurifier $purifier;

    public function __construct(array $allowedIframeDomains = [])
    {
        $config = HTMLPurifier_Config::createDefault();

        $config->set('HTML.SafeIframe', true);

        if (!empty($allowedIframeDomains)) {
            $escapedDomains = array_map(fn($d) => preg_quote($d, '%'), $allowedIframeDomains);
            $regexp = '%^(https?:)?//(' . implode('|', $escapedDomains) . ')/%';
            $config->set('URI.SafeIframeRegexp', $regexp);
        }

        $this->purifier = new HTMLPurifier($config);
    }

    public function clean(string $input): string
    {
        return $this->purifier->purify($input);
    }

    public function cleanArray(array $data): array
    {
        return array_map(fn($v) => is_string($v) ? $this->clean($v) : $v, $data);
    }
}