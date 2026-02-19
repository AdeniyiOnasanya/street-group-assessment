<?php

namespace App\Services;

use App\DTO\PersonDTO;

class PersonNameParserService
{
    private const TITLES = ['mister', 'mr', 'mrs', 'ms', 'miss', 'dr', 'prof',];

    public function parse(string $raw): array
    {
        $raw = $this->normalise($raw);

        if (str_contains($raw, ' and ')) {

            $parts = explode(' ', $raw);

            if (
                count($parts) === 4
                && $this->isTitlePart($parts[0])
                && strtolower($parts[1]) === 'and'
                && $this->isTitlePart($parts[2])
            ) {
                $lastName = $parts[3];

                return [
                    new PersonDTO(
                        title: $this->normaliseTitle($parts[0]),
                        first_name: null,
                        last_name: $lastName,
                        initial: null,
                    ),
                    new PersonDTO(
                        title: $this->normaliseTitle($parts[2]),
                        first_name: null,
                        last_name: $lastName,
                        initial: null,
                    ),
                ];
            }

            [$left, $right] = explode(' and ', $raw, 2);

            if ($this->startsWithTitle($left) && $this->startsWithTitle($right)) {
                return [
                    $this->parseSinglePerson(trim($left)),
                    $this->parseSinglePerson(trim($right)),
                ];
            }
        }

        if (str_contains($raw, ' & ')) {
            $parts = explode(' ', $raw);

            if (
                count($parts) === 5
                && $parts[1] === '&'
                && $this->isTitlePart($parts[0])
                && $this->isTitlePart($parts[2])
            ) {
                $firstName = $parts[3];
                $lastName  = $parts[4];
                $initials  = $this->computeInitials($firstName, $lastName);

                return [
                    new PersonDTO(
                        title: $this->normaliseTitle($parts[0]),
                        first_name: $firstName,
                        last_name: $lastName,
                        initial: $initials,
                    ),
                    new PersonDTO(
                        title: $this->normaliseTitle($parts[2]),
                        first_name: $firstName,
                        last_name: $lastName,
                        initial: $initials,
                    ),
                ];
            }
        }

        return [$this->parseSinglePerson($raw)];
    }

    private function normalise(string $raw): string
    {
        $raw = trim($raw);
        $raw = preg_replace('/\s+/', ' ', $raw) ?? $raw;

        $raw = str_replace('&amp;', '&', $raw);
        $raw = preg_replace('/\s*&\s*/', ' & ', $raw) ?? $raw;
        $raw = preg_replace('/\s+and\s+/i', ' and ', $raw) ?? $raw;

        $raw = preg_replace('/^Mister\b/i', 'Mr', $raw) ?? $raw;

        return trim($raw);
    }

    private function startsWithTitle(string $chunk): bool
    {
        $firstPart = explode(' ', trim($chunk))[0] ?? '';
        return $this->isTitlePart($firstPart);
    }

    private function isTitlePart(string $part): bool
    {
        return in_array(strtolower($part), self::TITLES, true);
    }

    private function normaliseTitle(string $title): string
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

    private function isInitialPart(string $part): bool
    {
        return (bool) preg_match('/^[A-Za-z]\.?$/', $part);
    }

    private function cleanInitial(string $part): string
    {
        return strtoupper(rtrim($part, '.'));
    }

    private function computeInitials(?string $firstName, ?string $lastName): ?string
    {
        if ($firstName === null || $lastName === null) {
            return null;
        }

        $first = mb_substr($firstName, 0, 1);
        $last  = mb_substr($lastName, 0, 1);

        if ($first === '' || $last === '') {
            return null;
        }

        return strtoupper($first . $last);
    }

    private function parseSinglePerson(string $chunk): PersonDTO
    {
        $chunk = trim($chunk);
        $parts = $chunk === '' ? [] : explode(' ', $chunk);

        $titleRaw = $parts[0] ?? 'Mr';
        $title = $this->normaliseTitle($titleRaw);

        if (count($parts) === 2) {
            return new PersonDTO(
                title: $title,
                first_name: null,
                last_name: $parts[1],
                initial: null,
            );
        }

        if (count($parts) >= 3) {
            $middlePart = $parts[1];
            $lastName = $parts[count($parts) - 1];

            $firstName = null;
            $initial = null;

            if ($this->isInitialPart($middlePart)) {
                $initial = $this->cleanInitial($middlePart);
            } else {
                $firstName = $middlePart;
                $initial = $this->computeInitials($firstName, $lastName);
            }

            return new PersonDTO(
                title: $title,
                first_name: $firstName,
                last_name: $lastName,
                initial: $initial,
            );
        }

        return new PersonDTO(
            title: $title,
            first_name: null,
            last_name: $parts[1] ?? '',
            initial: null,
        );
    }
}
