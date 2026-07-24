<?php

namespace App\Services;

class DestinationUrlPolicy
{
    /** @return array{string, string|null} */
    public function normalize(string $destinationUrl): array
    {
        $parts = parse_url($destinationUrl);

        return [
            strtolower($parts['scheme']).'://'.strtolower($parts['host'])
                .(isset($parts['port']) ? ':'.$parts['port'] : '')
                .($parts['path'] ?? '/'),
            $parts['query'] ?? null,
        ];
    }

    public function allows(string $destinationUrl): bool
    {
        $parts = parse_url($destinationUrl);

        if (! is_array($parts)
            || ! in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
            || ! isset($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])) {
            return false;
        }

        $host = strtolower(rtrim($parts['host'], '.'));

        if ($host === '' || $host === 'localhost' || str_ends_with($host, '.localhost') || ! str_contains($host, '.')) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return $this->isPublicIp($host);
        }

        $records = dns_get_record($host, DNS_A | DNS_AAAA);

        if (! is_array($records) || $records === []) {
            return false;
        }

        foreach ($records as $record) {
            $address = $record['ip'] ?? $record['ipv6'] ?? null;

            if (! is_string($address) || ! $this->isPublicIp($address)) {
                return false;
            }
        }

        return true;
    }

    private function isPublicIp(string $address): bool
    {
        return filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }
}
