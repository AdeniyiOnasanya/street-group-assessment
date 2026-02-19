<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ParseHomeownersFromCsvControllerTest extends TestCase
{

    public function test_that_it_parses_a_valid_csv_and_returns_json(): void
    {
        $file = new UploadedFile(
            base_path('tests/TestFiles/examples.csv'),
            'examples.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->postJson('/api/homeowners/parse', [
            'csv' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonIsArray();
        $response->assertJsonStructure([
            '*' => ['title', 'first_name', 'last_name', 'initial',],
        ]);
    }

    public function test_that_it_returns_validation_error_when_file_missing(): void
    {
        $response = $this->postJson('/api/homeowners/parse', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['csv']);
    }
}
