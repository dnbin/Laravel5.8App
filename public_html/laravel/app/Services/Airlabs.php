<?php


namespace App\Services;

/**
 * Class Airlabs
 * @package App\Services
 * @method airports(?array $parameters)
 * @method cities(?array $parameters)
 * @method countries(?array $parameters)
 * @method airlines(?array $parameters)
 */
class Airlabs {
    protected $api_key;
    protected $base_uri='http://airlabs.co/api/v6';

    public function __construct(string $api_key) {
        $this->api_key=$api_key;
    }

    /**
     * @param string $api_key
     */
    public function setApiKey( string $api_key ): void {
        $this->api_key = $api_key;
    }

    /**
     * @param string $base_uri
     */
    public function setBaseUri( string $base_uri ): void {
        $this->base_uri = rtrim(trim($base_uri),'/');
    }

    /**
     * @return string
     */
    public function getBaseUri(): string {
        return $this->base_uri;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call( $name, $arguments ) {
        $url = $this->base_uri . "/" . $name . "?";
        if(!empty($arguments)){
            $url.=http_build_query(array_merge(["api_key" => $this->api_key], $arguments[0]));
        }
        else{
            $url.=http_build_query(["api_key" => $this->api_key]);
        }

        return json_decode(file_get_contents($url));
    }
}
