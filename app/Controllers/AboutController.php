<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AboutController extends BaseController
{
    private const API_NAME = 'YOUR_PROJECT_NAME';

    private const API_VERSION = '1.0.0';

    public function handleAboutWebService(Request $request, Response $response): Response
    {

        $data = array(
            'api' => self::API_NAME,
            'version' => self::API_VERSION,
            'about' => 'Welcome! This is a Web service that provides information about keyboards and mice available on the market.',
            'authors' => 'FrostyBee, melswain',
            'resources' => [
                '/vendors' => [
                    'country' => 'Filters vendors based on their country of origin.',
                    'founded_after' => 'Must also be accompanied by a founded_before filter. Shows all vendors established after the provided date  (between founded_before and founded_after). The provided value must be a valid year.',
                    'founded_before' => 'Must also be accompanied by a founded_after filter. Shows all vendors established before the provided date (between founded_before and founded_after). The provided value must be a valid year.',
                    'keyboards_count' => 'Filters vendors by the available keyboards that they provide. Will display any vendors who have a keyboard count equivalent to the one provided. The provided value must be numerical.',
                    'lower_price_limit' => 'Must also be accompanied by an upper_price_limit filter. Shows all vendors whose keyboards\'s prices exist between the provided prices. The provided value must be numerical.',
                    'upper_price_limit' => 'Must also be accompanied by a lower_price_limit filter. Shows all vendors whose keyboards\'s prices exist between the provided prices. The provided value must be numerical.',
                ],
                '/vendors/{vendor_id}' => 'Returns a single vendor by its ID.',
                '/vendors/{vendor_id}/switches' => [
                    'type' => 'Filter vendors\'s switches by their switch type, i.e., linear, tactile, or clicky.',
                    'lower_actuation_force_limit' => 'Must also be accompanied by an upper_actuation_force_limit. Provides a range for the switches\'s actuation force value. the provided value must be numerical.',
                    'upper_actuation_force_limit' => 'Must also be accompanied by a lower_actuation_force_limit. Provides a range for the switches\'s actuation force value. the provided value must be numerical.',
                    'lower_travel_distance_limit' => 'Must also be accompanied by an upper_travel_distance_limit. Provides a range for the switches\'s total travel value. the provided value must be numerical.',
                    'upper_travel_distance_limit' => 'Must also be accompanied by a lower_travel_distance_limit. Provides a range for the switches\'s total travel value. the provided value must be numerical.',
                    'lifespan_minimum' => 'Filters the switches based on the minimum lifespan required, which is provided in millions (e.g., 50 or 80).',
                    'released_after' => 'Must also be accompanied by a released_before value. The provided value must be a date in the form of YYYY-mm-dd. Specifies a range for all movies to be released between.',
                    'released_before' => 'Must also be accompanied by a released_after value. The provided value must be a date in the form of YYYY-mm-dd. Specifies a range for all movies to be released between.'
                ],
                '/keyboards' => [
                    'connectivity' => 'Filter keyboards by their connection type, i.e., wired, wireless, or both.',
                    'switch_type' => 'Filter keyboard\'s switches by their switch type, i.e., linear, tactile, or clicky.',
                    'hotswappable' => 'Filter a keyboard based on whether it is hotswappable. A true value is either  "1", "true", "on" or "yes", and a false value is either "0", "false", "off" or "no".',
                    'weight_maximum' => 'Filter the keyboards by a maximum weight, in grams. The provided value must be numeric.',
                    'released_after' => 'Must also be accompanied by a released_before value. The provided value must be a date in the form of YYYY-mm-dd. Specifies a range for all movies to be released between.',
                    'released_before' => 'Must also be accompanied by a released_after value. The provided value must be a date in the form of YYYY-mm-dd. Specifies a range for all movies to be released between.',
                    'firmware_type' => 'Filter keyboards by their PCB firmware type, i.e., QMK or proprietary.'
                ],
                '/keyboards/{keyboard_id}' => 'Returns a single keyboard by its ID.',
                '/mice' => [
                    'polling_rate' => '',
                    'connection' => '',
                    'weight_minimum' => '',
                    'weight_maximum' => '',
                    'lower_price_limit' => 'Must also be accompanied by an upper_price_limit filter. Shows all vendors whose keyboards\'s prices exist between the provided prices. The provided value must be numerical.',
                    'upper_price_limit' => 'Must also be accompanied by a lower_price_limit filter. Shows all vendors whose keyboards\'s prices exist between the provided prices. The provided value must be numerical.',
                    'button_count' => '',
                    'rating' => ''
                ],
                '/mice/{mouse_id}' => 'Returns a single mouse by its ID.',
                '/mice/{mouse_id}/buttons' => [],
                '/layouts' => ''
            ]
        );

        return $this->renderJson($response, $data);
    }
}
