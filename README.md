# km-api

## 🔨 Project Overview

### Keyboards and Mice API

This web service provides information about keyboards and mice available on the market, along with information regarding their layouts, buttons, switches, vendors, and more. It provides filtering options to obtain all of this information!

### Tech Stack

- Framework: Slim
- Languages: PHP
- Tools: Composer

## 📚 API Resources and Filters

### `/vendors`

- `country`: Filters vendors based on their country of origin.
- `founded_after`: Must be used with `founded_before`. Shows vendors established after the provided year.
- `founded_before`: Must be used with `founded_after`. Shows vendors established before the provided year.
- `keyboards_count`: Filters vendors by the number of keyboards they provide. Value must be numeric.
- `lower_price_limit`: Must be used with `upper_price_limit`. Filters vendors by keyboard price range. Value must be numeric.
- `upper_price_limit`: Must be used with `lower_price_limit`. Filters vendors by keyboard price range. Value must be numeric.
- `order_by`: Supports ascending sorting by any column in the vendors table (vendor_id, name, country, founded_year, website, headquarters).

### `/vendors/{vendor_id}`

Returns a single vendor by its ID.

### `/vendors/{vendor_id}/switches`

- `type`: Filter switches by type (linear, tactile, clicky).
- `lower_actuation_force_limit`: Must be used with `upper_actuation_force_limit`. Value must be numeric.
- `upper_actuation_force_limit`: Must be used with `lower_actuation_force_limit`. Value must be numeric.
- `lower_travel_distance_limit`: Must be used with `upper_travel_distance_limit`. Value must be numeric.
- `upper_travel_distance_limit`: Must be used with `lower_travel_distance_limit`. Value must be numeric.
- `lifespan_minimum`: Minimum lifespan in millions (e.g., 50 or 80).
- `released_after`: Must be used with `released_before`. Date format: `YYYY-mm-dd`.
- `released_before`: Must be used with `released_after`. Date format: `YYYY-mm-dd`.

### `/keyboards`

- `connectivity`: Filter by connection type (wired, wireless, both).
- `switch_type`: Filter by switch type (linear, tactile, clicky).
- `hotswappable`: Accepts `"1"`, `"true"`, `"on"`, `"yes"` for true; `"0"`, `"false"`, `"off"`, `"no"` for false.
- `weight_maximum`: Maximum weight in grams. Value must be numeric.
- `released_after`: Must be used with `released_before`. Date format: `YYYY-mm-dd`.
- `released_before`: Must be used with `released_after`. Date format: `YYYY-mm-dd`.
- `firmware_type`: Filter by PCB firmware type (QMK or proprietary).
- `order_by`: Supports ascending sorting by any column in the keyboards table (vendor_id, switch_id, layout_id, name, release_date, price, connectivity, hot_swappable, case_material, weight).

### `/keyboards/{keyboard_id}`

Returns a single keyboard by its ID.

### `/mice`

- `polling_rate`: Must be 125, 500, or 1000. Value must be numeric.
- `connection`: Filter by connection type (wired, wireless, both).
- `weight_minimum`: Must be used with `weight_maximum`. Value must be numeric.
- `weight_maximum`: Must be used with `weight_minimum`. Value must be numeric.
- `lower_price_limit`: Must be used with `upper_price_limit`. Value must be numeric.
- `upper_price_limit`: Must be used with `lower_price_limit`. Value must be numeric.
- `button_count`: Filters by number of buttons. Value must be numeric.
- `rating`: Minimum average rating. Value must be numeric.

### `/mice/{mouse_id}`

Returns a single mouse by its ID.

### `/mice/{mouse_id}/buttons`

- `name`: Filters buttons similar to the provided name.
- `programmable`: Accepts `"1"`, `"true"`, `"on"`, `"yes"` for true; `"0"`, `"false"`, `"off"`, `"no"` for false.

### `/layouts`

Returns all layouts. No filters supported.

### `/layouts/{layout_id}`

Returns a single layout by its ID.

### `/layouts/{layout_id}/keyboards`

- `switch_type`: Filter layout's keyboards by their switch type, i.e., linear, tactile, or clicky.
- `lower_price_limit`: Must be used with `upper_price_limit`. Value must be numeric.
- `upper_price_limit`: Must be used with `lower_price_limit`. Value must be numeric.
- `connectivity`: Filter by connection type (wired, wireless, both).

### `/layouts/{layout_id}/keycap-sets`

- `material`: Filter by keycap material.
- `profile`: Filter by keycap profile.
- `manufacturer`: Filter by keycap manufacturer.
- `price_maximum`: Maximum price. Value must be numeric.

## ❌ Errors

- `HttpInvalidDateException`: thrown when the inputted date does not match the format YYYY-mm-dd
- `HttpInvalidIdException`: thrown when the provided id of a resource is invalid or does not exist
- `HttpInvalidParameterException`: thrown when a query parameter does not exist
- `HttpInvalidParameterValueException`: thrown when a query parameter's value is invalid and does not match the value options
- `HttpPaginationException`: thrown when the value inputted for pagination is invalid, like a string
- `HttpRangeFilterException`: thrown when the value for an upper limit is provided but not the lower, and vice-versa
