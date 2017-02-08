<?php
namespace ImmediateSolutions;

use GuzzleHttp\Client;
use RuntimeException;

class Locator
{
    const USERNAME = 'ivorobiov';

    /**
     * @param string $location
     * @param int $distance
     * @return array
     */
    public function places($location, $distance)
    {
        $client = new Client();

        $query = [
            'country' => 'AU',
            'radius' => $distance,
            'username' => self::USERNAME,
            'style' => 'long',
            'maxRows' => 100
        ];

        if (is_numeric($location)){
            $query['postalcode'] = $location;
        } else {
            $query['placename'] = $location;
        }

        $response = $client->get('http://api.geonames.org/findNearbyPostalCodesJSON', ['query' => $query]);

        $json = (string) $response->getBody();

        $data = json_decode($json, true);

        if ($data === null){
            throw new RuntimeException('JSON is expected but something else is given');
        }

        if (!isset($data['postalCodes'])){
            throw new RuntimeException($data['status']['message'] ?? 'Unknown JSON is given');
        }

        return array_map(function($item){
            return [
                'name' => $item['placeName'],
                'code' => $item['postalCode']
            ];
        }, $data['postalCodes']);
    }
}

