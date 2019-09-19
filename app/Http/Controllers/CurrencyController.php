<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Transformers\BaseTransformer;
use App\Models\Currency;
use DB;
use Redis;
use Specialtactics\L5Api\Http\Controllers\Features\JWTAuthenticationTrait;
use Str;

class CurrencyController extends Controller
{
    use JWTAuthenticationTrait;

    /**
     * @var BaseModel The primary model associated with this controller
     */
    public static $model = Currency::class;

    /**
     * @var BaseModel The parent model of the model, in the case of a child rest controller
     */
    public static $parentModel = null;

    /**
     * @var null|BaseTransformer The transformer this controller should use, if overriding the model & default
     */
    public static $transformer = null;

    /**
     * convert currencies from $source into $target
     *
     * @param $source
     * @param $target
     * @param $amount
     *
     * @return \Dingo\Api\Http\Response|void
     */
    public function convert($source, $target, $amount){
        $response = new \Stdclass;

        //Sanity checks
        if(Str::contains($amount, ",")){
            return $this->response->errorBadRequest("Invalid amount to calculate has been found - comma detected.");
        }

        if($source === $target){
            $response->amount = (float)$amount;
            return $this->response->item($response, $this->getTransformer())->setStatusCode(200);
        }

        $currencyName = sprintf("%s%s", $source, $target);
        $currencyRate = Redis::hget("currency:" . $currencyName, "exchange_rate");

        if(!isset($currencyRate)){
            $currency = DB::table('currencies')->select('exchange_rate')->where('source', $source)->where('target', $target)->first();
            if(!isset($currency)){
                return $this->response->errorNotFound("Currency pair does not exist");
            }

            $currencyRate = $currency->exchange_rate;
            Redis::hset("currency:" . $currencyName, "exchange_rate", $currencyRate);
        }

        $response->amount = $currencyRate * $amount;

        return $this->response->item($response, $this->getTransformer())->setStatusCode(200);
    }
}
