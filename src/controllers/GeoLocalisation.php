<?php
class GeoLocalisation
{
    public function getCoordinates(string $city): ?array
    {
        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($city) .
            "&format=json&limit=1";

        $options = [
            "http" => [
                "header" => "User-Agent: Loove/1.0 (test@mail.com)\r\n",
                "ignore_errors" => true
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);

        if (!empty($data)) {
            return [
                'longitude' => (float) $data[0]['lat'],
                'latitude' => (float) $data[0]['lon']
            ];
        }

        return null;
    }

    public function getCity(float $longitude, float $latitude)
    {
        $url = "https://nominatim.openstreetmap.org/reverse?lat=" . $latitude .
            "&lon=" . $longitude . "&format=json";

        $options = [
            "http" => [
                "header" => "User-Agent: Loove/1.0 (quas.cia450@gmail.com)\r\n"
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if (!$response)
            return null;

        $data = json_decode($response, true);
        if (isset($data['address'])) {
            return $data['address']['city']
                ?? $data['address']['town']
                ?? $data['address']['village']
                ?? $data['adress']['hamlet']
                ?? $date['adress']['municipality']
                ?? 'Unknown location';
        }

        return null;

    }
}