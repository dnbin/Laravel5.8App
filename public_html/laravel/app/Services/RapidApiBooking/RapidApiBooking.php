<?php


namespace App\Services\RapidApiBooking;

use Unirest\Request;
use Unirest\Response;

/**
 * Class RapidApiBooking
 * @package Services
 */
class RapidApiBooking {
    protected $api_key;
    protected $host;
    protected $url;
    protected $headers=[];

    public function __construct(string $api_key,string $host){
        $this->api_key=$api_key;
        $this->host=$host;
        $this->url='https://'.$this->host;
        $this->headers=            [
            "X-RapidAPI-Host" => $this->host,
            "X-RapidAPI-Key" => $this->api_key
        ];
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     * @throws ApiException
     * @throws ServerException
     */
    public function getPropertiesList(array $parameters){
        $response = Request::get($this->url."/properties/list?".http_build_query($parameters),
            $this->headers
        );
        $this->checkResponseError($response);
        return $response->body;
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     * @throws ApiException
     * @throws ServerException
     */
    public function getLocationsAutoComplete(array $parameters){
        $response = Request::get($this->url."/locations/auto-complete?".http_build_query($parameters),
            $this->headers
        );
        $this->checkResponseError($response);
        return $response->body;
    }

    /** dest_type=city */
    /**
     * @param string $dest_type
     * @param array $parameters
     *
     * @return int|null
     * @throws ApiException
     * @throws ServerException
     */
    public function getDestId(string $dest_type,array $parameters):?int{
        $response=$this->getLocationsAutoComplete($parameters);
        if(!empty($response)){
            foreach($response as $r){
                if($r->dest_type===$dest_type){
                    return (int)$r->dest_id;
                }
            }
        }
        return null;
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     * @throws ApiException
     * @throws ServerException
     */
    public function getCurrencyExchangeRates(array $parameters){
        $response = Request::get($this->url."/currency/get-exchange-rates?".http_build_query($parameters),
            $this->headers
        );
        $this->checkResponseError($response);
        return $response->body;
    }

    /**
     * @param Response $response
     *
     * @throws ApiException
     * @throws ServerException
     */
    protected function checkResponseError(Response $response){
        if($response->code!==200){
            throw new ServerException($response->code);
        }

        if(!empty($response->body->code) && $response->body->code!==200){
            dump($response);
            throw new ApiException($response->body->message,$response->body->code);
        }
    }

    public function getPriceCategories(int $price){
        $price_categories=[];
        if($price>0){
            $price_categories[]='price_category::50';
        }
        if($price>50){
            $price_categories[]='price_category::100';
        }

        if($price>110){
            $price_categories[]='price_category::150';
        }
        if($price>160){
            $price_categories[]='price_category::200';
        }
        if($price>220){
            $price_categories[]='price_category::250';
        }

        return $price_categories;
    }
}
