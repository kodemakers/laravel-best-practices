<?php

namespace App\Traits;

use AnthonyMartin\GeoLocation\Earth;
use AnthonyMartin\GeoLocation\GeoPoint;

trait GeoGenerate
{
    private function latLongFromDistance(GeoPoint $geopoint, $distance, $bearingInDeg, $unit_of_measurement)
    {
        $radius = Earth::getRadius($unit_of_measurement);

        // $radius = 6378.1; // Radius of the Earth
        $bearing = $bearingInDeg * M_PI / 180; // Convert bearing to radian

        // angular distance in radians on a great circle
        $angularDistance = $distance / $radius;

        // Do the math magic
        $newLat = asin(sin($geopoint->getLatitude(true)) * cos($angularDistance) + cos($geopoint->getLatitude(true)) * sin($angularDistance) * cos($bearing));
        $newLon = $geopoint->getLongitude(true) + atan2(sin($bearing) * sin($angularDistance) * cos($geopoint->getLatitude(true)), cos($angularDistance) - sin($geopoint->getLatitude(true)) * sin($geopoint->getLatitude(true)));

        return new GeoPoint($newLat, $newLon, true);
    }

    public function boundingBoxCircle(GeoPoint $geopoint, $distance, $unit_of_measurement, $numberOfPoints)
    {
        $points = [];
        for ($i = 0; $i <= $numberOfPoints - 1; $i++) {
            $bearing = round((360 / $numberOfPoints) * $i);
            $newPoints = $this->latLongFromDistance($geopoint, $distance, $bearing, $unit_of_measurement);
            $points[] = $newPoints;
        }

        return $points;
    }
}
