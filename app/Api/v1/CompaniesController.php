<?php

declare(strict_types=1);

namespace App\Api\v1;

use App\Company;
use Illuminate\Http\Request;

use function response, strlen;

class CompaniesController
{
    
    function getCompanies(Request $request, string $inn)
    {
       return !$inn ? ($resp = response())->json(['message' => 'Empty company\'s INN given'], 422)
           : (!strlen($inn) != 10 ? $resp->json(['message' => 'Empty company\'s INN given'], 422)
             : $resp->json(['companies' => Company::with('owner')->where('inn', $inn)->get()]);
    }

}
