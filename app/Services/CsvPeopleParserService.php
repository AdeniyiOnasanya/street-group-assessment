<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use SplFileObject;

class CsvPeopleParserService
{
    public function __construct( private PersonNameParserService $personNameParser,) {}

    public function parseUploadedCsv(UploadedFile $file, string $columnName): array
    {
        $csv = new SplFileObject($file->getPathname());
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

        $header = null;
        $columnIndex = null;

        $people = [];

        foreach ($csv as $row) {
            if (!$this->isDataRow($row)) {
                continue;
            }

            if ($header === null) {
                $header = $this->cleanHeader($row);
                $columnIndex = $this->findColumnIndex($header, $columnName);

                if ($columnIndex === null) {
                    throw ValidationException::withMessages([
                        'csv' => ["CSV must include a $columnName header column."],
                        'headers_found' => $header,
                    ]);
                }

                continue;
            }

            $raw = $this->stringCell($row[$columnIndex] ?? null);
            if ($raw === '') {
                continue;
            }

            foreach ($this->personNameParser->parse($raw) as $dto) {
                $people[] = $dto->toArray();
            }
        }

        return $people;
    }

    private function isDataRow(mixed $row): bool
    {
        return is_array($row) && !(count($row) === 1 && $row[0] === null);
    }

    private function cleanHeader(array $row): array
    {
        return array_map(
            static fn ($header) => is_string($header) ? trim($header) : '',
            $row
        );
    }

    private function findColumnIndex(array $header, string $required): ?int
    {
        $lower = array_map(
            static fn ($h) => strtolower((string) $h),
            $header
        );

        $idx = array_search(strtolower($required), $lower, true);

        return $idx === false ? null : $idx;
    }

    private function stringCell(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }
}
