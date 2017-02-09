<?php
namespace ImmediateSolutions;
use PDO;

class Locator
{
    const EARTH_RADIUS = 6371.009;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string $location
     * @param int $distance
     * @return array
     */
    public function places($location, $distance)
    {
        if (is_numeric($location)){
            $constraint = 'postcode=?';
        } else {
            $location = strtoupper($location);
            $constraint = 'suburb=?';
        }


        $stm = $this->pdo->prepare('SELECT * FROM postcode_db WHERE '.$constraint);
        $stm->execute([$location]);
        $data = $stm->fetch(PDO::FETCH_ASSOC);

        if (!$data){
            return [];
        }

        $lat = (float) $data['lat'];
        $lng = (float) $data['lon'];

        $maxLat = (float) $lat + rad2deg($distance / self::EARTH_RADIUS);
        $minLat = (float) $lat - rad2deg($distance / self::EARTH_RADIUS);

        $maxLng = (float) $lng + rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad((float) $lat)));
        $minLng = (float) $lng - rad2deg($distance / self::EARTH_RADIUS / cos(deg2rad((float) $lat)));


        $stm = $this->pdo->prepare('SELECT * FROM postcode_db WHERE lat > ? AND lat < ? AND lon > ? AND lon < ? ORDER BY ABS(lat - ?) + ABS(lon - ?) ASC');
        $stm->execute([$minLat, $maxLat, $minLng, $maxLng, $lat, $lng]);

        $result = [];

        while ($item = $stm->fetch(PDO::FETCH_ASSOC)){
            if ($item['postcode'] < 1000 || $item['postcode'] >= 2000){
                $result[] = [
                    'name' => $item['suburb'],
                    'code' => $item['postcode']
                ];
            }
        }

        return $result;
    }
}

