<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AboutController extends BaseController
{
    private const API_NAME = 'YOUR_PROJECT_NAME';

    private const API_VERSION = '1.0.0';

    /**
     * Displays information about the web service
     * Includes all the exposed resources and what filters can be used on them
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @return Response The encoded response to be sent to the user
     */
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
                    'order_by' => 'Supports ascending sorting by any column in the vendors table (vendor_id, name, country, founded_year, website, headquarters).'
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
                    'firmware_type' => 'Filter keyboards by their PCB firmware type, i.e., QMK or proprietary.',
                    'order_by' => 'Supports ascending sorting by any column in the keyboards table (vendor_id, switch_id, layout_id, name, release_date, price, connectivity, hot_swappable, case_material, weight).'
                ],
                '/keyboards/{keyboard_id}' => 'Returns a single keyboard by its ID.',
                '/mice' => [
                    'polling_rate' => 'Filter by the mice\'s polling rate. The provided value must be numeric and must be either 125, 500, or 1000.',
                    'connection' => 'Filter by the mice\'s connectin type. Can either be wired, wireless, or both.',
                    'weight_minimum' => 'Filter the keyboards by a minimum weight, in grams. Must also be accompanied by a corresponding weight maximum. The provided value must be numeric.',
                    'weight_maximum' => 'Filter the keyboards by a maximum weight, in grams. Must also be accompanied by a corresponding weight minimum. The provided value must be numeric.',
                    'lower_price_limit' => 'Must also be accompanied by an upper_price_limit filter. Shows all vendors whose keyboards\'s prices exist between the provided prices. The provided value must be numerical.',
                    'upper_price_limit' => 'Must also be accompanied by a lower_price_limit filter. Shows all vendors whose keyboards\'s prices exist between the provided prices. The provided value must be numerical.',
                    'button_count' => 'Filters the mice by the amount of buttons they possess. This corresponds to their count of occurrence in the mouse_buttons table. The provided value must be numeric.',
                    'rating' => 'Shows all mice having an average rating equal to or higher than the value provided. The provided value must be numeric.'
                ],
                '/mice/{mouse_id}' => 'Returns a single mouse by its ID.',
                '/mice/{mouse_id}/buttons' => [
                    'name' => 'Shows buttons similar to the provided name.',
                    'programmable' => 'Filter a button based on whether it is programmable. A true value is either  "1", "true", "on" or "yes", and a false value is either "0", "false", "off" or "no".'
                ],
                '/layouts' => 'Returns all layouts with no supported filters.',
                '/layouts/{layout_id}' => 'Returns a single layout by its ID.',
                '/layouts/{layout_id}/keyboards' => [
                    'switch_type' => 'Filter layout\'s keyboards by their switch type, i.e., linear, tactile, or clicky.',
                    'lower_price_limit' => 'Must also be accompanied by an upper_price_limit filter. Shows all keyboard layouts whose layout\'s prices exist between the provided prices. The provided value must be numerical.',
                    'upper_price_limit' => 'Must also be accompanied by a lower_price_limit filter. Shows all keyboard layouts whose layout\'s prices exist between the provided prices. The provided value must be numerical.',
                    'connectivity' => 'Filter keyboards by their connection type, i.e., wired, wireless, or both.'
                ],
                '/layouts/{layout_id}/keycap-sets' => [
                    'material' => 'Filters layout keycap sets by their material.',
                    'profile' => 'Filters layout keycap sets by their profile.',
                    'manufacturer' => 'Filters layout keycap sets by their manufacturer.',
                    'price_maximum' => 'Shows all the keycap sets bellow the provided price. The provided value must be numeric.'
                ]
            ]
        );

        return $this->renderJson($response, $data);
    }
}
