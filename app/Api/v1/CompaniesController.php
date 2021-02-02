<?php

namespace App\Api\v1;

use App\Company;
use Illuminate\Http\Request;

class CompaniesController
{

    function getCompanies(Request $request, string $inn)
    {
        if(!$inn)
        {
            return response()->json(['message' => 'Empty company\'s INN given'], 422);
        }

        if(strlen($inn) != 10)
        {
            return response()->json(['message' => 'Invalid company\'s INN given'], 422);
        }

        $companies = Company::with('owner')->where('inn', $inn)->get();

        return response()->json(['companies' => $companies]);
    }

}
