<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParseHomeownersFromCsvRequest;
use App\Services\CsvPeopleParserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class ParseHomeownersFromCsvController extends Controller
{
    public function __construct(private readonly CsvPeopleParserService $service){}

    public function __invoke(ParseHomeownersFromCsvRequest $request): JsonResponse
    {
        $homeowners = $this->service->parseUploadedCsv($request->validated('csv'), 'homeowner');

        return Response::json($homeowners);
    }
}
