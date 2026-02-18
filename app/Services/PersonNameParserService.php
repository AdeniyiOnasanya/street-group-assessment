<?php

namespace App\Services;

use App\DTO\PersonDTO;

final class PersonNameParserService
{
    public function parse(string $raw): array
    {
        $raw = $this->normalize($raw);

        if (str_contains($raw, ' and ')) {
            [$left, $right] = explode(' and ', $raw, 2);

            if ($this->startsWithTitle($left) && $this->startsWithTitle($right)) {
                return [
                    $this->parseSinglePerson(trim($left)),
                    $this->parseSinglePerson(trim($right)),
                ];
            }

            $tokens = explode(' ', $raw);
            if (count($tokens) === 4 && $this->isTitleToken($tokens[0]) && $this->isTitleToken($tokens[2])) {
                $surname = $tokens[3];

                return [
                    new PersonDTO($this->normalizeTitle($tokens[0]), null, null, $surname),
                    new PersonDTO($this->normalizeTitle($tokens[2]), null, null, $surname),
                ];
            }
        }

        if (str_contains($raw, ' & ')) {
            $tokens = explode(' ', $raw);

            if (
                count($tokens) === 5
                && $tokens[1] === '&'
                && $this->isTitleToken($tokens[0])
                && $this->isTitleToken($tokens[2])
            ) {
                $first = $tokens[3];
                $last  = $tokens[4];
                $initials = $this->computeInitials($first, $last);

                return [
                    new PersonDTO($this->normalizeTitle($tokens[0]), $first, $initials, $last),
                    new PersonDTO($this->normalizeTitle($tokens[2]), $first, $initials, $last),
                ];
            }
        }

        return [$this->parseSinglePerson($raw)];
    }

    private function normalize(string $raw): string
    {
        $raw = trim($raw);
        $raw = preg_replace('/\s+/', ' ', $raw) ?? $raw;

        $raw = str_replace('&amp;', '&', $raw);
        $raw = preg_replace('/\s*&\s*/', ' & ', $raw) ?? $raw;
        $raw = preg_replace('/\s+and\s+/i', ' and ', $raw) ?? $raw;

        // dataset includes "Mister"
        $raw = preg_replace('/^Mister\b/i', 'Mr', $raw) ?? $raw;

        return trim($raw);
    }

    private function startsWithTitle(string $chunk): bool
    {
        $first = explode(' ', trim($chunk))[0] ?? '';
        return $this->isTitleToken($first);
    }

    private function isTitleToken(string $token): bool
    {
        return in_array(strtolower($token), ['mr', 'mrs', 'ms', 'miss', 'dr', 'prof', 'mister'], true);
    }

    private function normalizeTitle(string $title): string
    {
        return match (strtolower($title)) {
            'mr' => 'Mr',
            'mrs' => 'Mrs',
            'ms' => 'Ms',
            'miss' => 'Miss',
            'dr' => 'Dr',
            'prof' => 'Prof',
            'mister' => 'Mr',
            default => $title,
        };
    }

    private function isInitialToken(string $token): bool
    {
        return (bool) preg_match('/^[A-Za-z]\.?$/', $token);
    }

    private function cleanInitial(string $token): string
    {
        return strtoupper(rtrim($token, '.'));
    }

    private function computeInitials(?string $firstName, ?string $lastName): ?string
    {
        if ($firstName === null || $lastName === null) {
            return null;
        }

        $f = mb_substr($firstName, 0, 1);
        $l = mb_substr($lastName, 0, 1);

        if ($f === '' || $l === '') {
            return null;
        }

        return strtoupper($f . $l);
    }

    private function parseSinglePerson(string $chunk): PersonDTO
    {
        $chunk = trim($chunk);
        $parts = $chunk === '' ? [] : explode(' ', $chunk);

        $titleRaw = $parts[0] ?? 'Mr';
        $title = $this->normalizeTitle($titleRaw);

        if (count($parts) === 2) {
            return new PersonDTO($title, null, null, $parts[1]);
        }

        if (count($parts) >= 3) {
            $middle = $parts[1];
            $last = $parts[count($parts) - 1];

            $firstName = null;
            $initial = null;

            if ($this->isInitialToken($middle)) {
                $initial = $this->cleanInitial($middle);
            } else {
                $firstName = $middle;
                $initial = $this->computeInitials($firstName, $last);
            }

            return new PersonDTO($title, $firstName, $initial, $last);
        }

        return new PersonDTO($title, null, null, $parts[1] ?? '');
    }
}
