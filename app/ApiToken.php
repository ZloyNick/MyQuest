<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ApiToken extends Model
{

    protected $table = 'api_tokens';

    protected $fillable = [
        'api_token'
    ];

    public $timestamps = false;

    protected $hidden = [
      'id'
    ];

    /**
     *
     * <p>
     *  Generates given number of tokens
     * </p>
     *
     * @param int $num
     * @return Collection
     */
    static function forceCreate(int $num = 1) : Collection
    {
        $collection = new Collection;

        for($i = 0; $i < $num; $i++)
        {
            $collection->add(static::generateToken());
        }

        return $collection;
    }

    /**
     *
     * <title>
     *  Token creation
     * </title>
     *
     * @param bool $save
     * @return ApiToken
     *
     */
    static function generateToken(bool $save = true)
    {
        $token = new static();
        $token->api_token = Str::random(80);

        if($save)
            $token->save();

        return $token;
    }
}
