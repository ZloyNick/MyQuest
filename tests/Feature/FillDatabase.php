<?php

/**
 * Status: Done!
 */

namespace Tests\Feature;

use App\Company;
use App\Maintrainer;

class FillDatabase extends PthreadsAsyncRequest
{
    /**
     * A basic feature test example.
     *
     * @return array
     */
    public function test_example($inn = 2310031475)
    {

        $data1 = parent::test_example();
        $data2 = parent::test_example(7707083893);

        if(Maintrainer::all()->count() > 0)
            return [];

        foreach ($data1 as $k => $v)
        {
            foreach ($v as $n => $company)
            {
                $company['inn'] = $data2[$k][$n]['inn'];
                $maintrainer = $company['maintrainer'];
                $m = Maintrainer::create($maintrainer);
                $company['maintrainer'] = $m->id;
                Company::create($company);
            }
        }

        foreach ($data2 as $k => $v)
        {
            foreach ($v as $n => $company)
            {
                $company['inn'] = $data1[$k][$n]['inn'];
                $maintrainer = $company['maintrainer'];
                $m = Maintrainer::create($maintrainer);
                $company['maintrainer'] = $m->id;
                Company::create($company);
            }
        }

        $this->assertTrue(Company::all()->count() > 1);

        return [];
    }
}
