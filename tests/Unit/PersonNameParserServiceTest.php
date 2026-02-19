<?php

namespace Tests\Unit;

use App\Services\PersonNameParserService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonNameParserServiceTest extends TestCase
{
    private PersonNameParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new PersonNameParserService();
    }

    #[Test]
    public function it_parses_a_single_person(): void
    {
        $response = $this->parser->parse('Mr John Smith');

        $this->assertCount(1, $response);
        $this->assertSame('Mr', $response[0]->title);
        $this->assertSame('John', $response[0]->first_name);
        $this->assertSame('JS', $response[0]->initial);
        $this->assertSame('Smith', $response[0]->last_name);
    }

    #[Test]
    public function it_parses_explicit_initial(): void
    {
        $response = $this->parser->parse('Dr P Gunn');

        $this->assertCount(1, $response);
        $this->assertSame('Dr', $response[0]->title);
        $this->assertNull($response[0]->first_name);
        $this->assertSame('P', $response[0]->initial);
        $this->assertSame('Gunn', $response[0]->last_name);
    }

    #[Test]
    public function it_parses_mr_and_mrs_shared_surname(): void
    {
        $response = $this->parser->parse('Mr and Mrs Smith');
        [$mr,$mrs] = $response;

        $this->assertCount(2, $response);

        $this->assertSame('Mr', $mr->title);
        $this->assertNull($mr->first_name);
        $this->assertNull($mr->initial);
        $this->assertSame('Smith', $mr->last_name);

        $this->assertSame('Mrs', $mrs->title);
        $this->assertNull($mrs->first_name);
        $this->assertNull($mrs->initial);
        $this->assertSame('Smith', $mrs->last_name);
    }

    #[Test]
    public function it_parses_two_full_names_separated_by_and(): void
    {
        $response = $this->parser->parse('Mr Tom Staff and Mr John Doe');
        [$mr,$mrs] = $response;

        $this->assertCount(2, $response);

        $this->assertSame('Mr', $mr->title);
        $this->assertSame('Tom', $mr->first_name);
        $this->assertSame('TS', $mr->initial);
        $this->assertSame('Staff', $mr->last_name);

        $this->assertSame('Mr', $mrs->title);
        $this->assertSame('John', $mrs->first_name);
        $this->assertSame('JD', $mrs->initial);
        $this->assertSame('Doe', $mrs->last_name);
    }

    #[Test]
    public function it_parses_dr_ampersand_mrs_shared_first_and_last(): void
    {
        $response = $this->parser->parse('Dr & Mrs Joe Bloggs');
        [$mr,$mrs] = $response;

        $this->assertCount(2, $response);

        $this->assertSame('Dr', $mr->title);
        $this->assertSame('Joe', $mr->first_name);
        $this->assertSame('JB', $mr->initial);
        $this->assertSame('Bloggs', $mr->last_name);

        $this->assertSame('Mrs', $mrs->title);
        $this->assertSame('Joe', $mrs->first_name);
        $this->assertSame('JB', $mrs->initial);
        $this->assertSame('Bloggs', $mrs->last_name);
    }

    #[Test]
    public function it_normalises_mister_to_mr(): void
    {
        $response = $this->parser->parse('Mister John Doe');

        $this->assertCount(1, $response);
        $this->assertSame('Mr', $response[0]->title);
        $this->assertSame('John', $response[0]->first_name);
        $this->assertSame('JD', $response[0]->initial);
        $this->assertSame('Doe', $response[0]->last_name);
    }
}
