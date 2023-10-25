<?php

/*
 * ******** Муниципалитеты / СНТ ***********
 */

function getMunicipality()
{
    $fields = get_fields('option');

    foreach ($fields['city'] as $key => $value) {

        if (is_array($value['municipality'])) {
            foreach ($value['municipality'] as $k => $v) {

                $municipality[$value['name_city']][] = [
                    'city' => $value['name_city'],
                    'municipality' => $v['name_municipality'],
                    'id_municipality' => $v['id_municipality'],
                    'map_svg' => $v['map_svg'],
                    'square' => $v['square'],
                    'population' => $v['population'],
                    'land_plots' => $v['land_plots'],
                    'number_snt' => $v['number_snt'],
                    'snt' => $v['snt'],
                ];
            }
        } else {

            $square = '';
            $id_municipality = '';
            $population = '';
            $land_plots = '';
            $number_snt = '';
            $map_svg = '';

            if (isset($value['id_municipality'])) {
                $id_municipality = $value['id_municipality'];
            }
            if (isset($value['square'])) {
                $square = $value['square'];
            }
            if (isset($value['population'])) {
                $square = $value['population'];
            }
            if (isset($value['land_plots'])) {
                $land_plots = $value['land_plots'];
            }
            if (isset($value['number_snt'])) {
                $number_snt = $value['number_snt'];
            }
            if (isset($value['map_svg'])) {
                $map_svg = $value['map_svg'];
            }


            $municipality[$value['name_city']][] = [
                'city' => $value['name_city'],
                'municipality' => $value['municipality'],
                'id_municipality' => $id_municipality,
                'map_svg' => $map_svg,
                'square' => $square,
                'population' => $population,
                'land_plots' => $land_plots,
                'number_snt' => $number_snt,
                'snt' => '',
            ];
        }
    }

    return $municipality;
}

function getSntAll()
{
    $municipality = getMunicipality();

    foreach ($municipality as $f => $r) {

        foreach ($r as $i => $w) {

            if (is_array($w['snt'])) {

                foreach ($w['snt'] as $s => $m) {
                    $res[] = [
                        'city' => $w['city'],
                        'municipality' => $w['municipality'],
                        'id_municipality' => $w['id_municipality'],
                        'map_svg' => $w['map_svg'],
                        'snt' => $m['name_snt'],
                        'link_maps' => $m['link_maps'],
                        'id_group' => $m['id_group'],
                    ];
                }
            } else {
                $res[] = [
                    'city' => $w['city'],
                    'municipality' => $w['municipality'],
                    'id_municipality' => $w['id_municipality'],
                    'map_svg' => $w['map_svg'],
                    'snt' => '',
                    'link_maps' => '',
                    'id_group' => '',
                ];
            }
        }
    }

    return $res;
}

function getSntByName($snt)
{
    foreach (getSntAll() as $key => $value) {
        if ($value['snt'] === $snt)
            $data = $value;
    }
    if (!empty($data)) {
        return $data;
    }
    return null;
}

function getSntByMunicipalityId($id_municipality)
{
    foreach (getSntAll() as $key => $value) {
        if ($value['id_municipality'] === $id_municipality)
            $data[] = $value;
    }
    if (!empty($data)) {
        return $data;
    }
    return null;
}

function getSntByIdMunicipalityJson($id_municipality)
{
    $municipality = getMunicipalityById($id_municipality);
    $data['map_svg'] = $municipality['map_svg'];

    foreach (getSntAll() as $key => $value) {

        if ($value['id_municipality'] === $id_municipality)

            if (is_user_logged_in() && !empty($value['link_maps'])) {
                $data['snt'][] = [
                    'link_maps' => $value['link_maps'],
                    'html' => '<a href="' . $value['link_maps']['url'] . '" target="_blank" class="map-step__last-text-item active" >' . $value['snt'] . '</a>',
                    // 'html' => '<div class="map-step__last-text-item active" data-step-next="' . sanitize_title_with_dashes(transliterate($value['snt'])) . '">' . $value['snt'] . '</div>',
                ];
            } elseif (is_user_logged_in() && empty($value['link_maps'])) {
                $data['snt'][] = [
                    'link_maps' => $value['link_maps'],
                    'html' => '<div class="map-step__last-text-item">' . $value['snt'] . '</div>',
                ];
            } elseif (!is_user_logged_in()) {
                $data['snt'][] = [
                    'link_maps' => $value['link_maps'],
                    'html' => '<div class="map-step__last-text-item active" data-bs-toggle="modal" data-bs-target="#authMap">' . $value['snt'] . '</div>',
                ];
            }
    }
    if (!empty($data)) {
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }
    return null;
}

/*   Подготовка PostId к запросу  ------------------------------*/

function cleanPostId($text)
{
    return str_replace(
        SEARCH_NAME_FIELD,
        '', preg_replace('/_/', '', $text,
            1
        )
    );
}

function updatingFieldIdGroup()
{
    $key_field = KEY_FIELD;
    global $wpdb;
    $results = $wpdb->get_results("SELECT option_name, option_value from $wpdb->options WHERE option_value = '$key_field'", ARRAY_A);
    $data = [];
    foreach ($results as $k => $v) {
        $data[] = [
            'post_id' => cleanPostId($v['option_name']),
        ];
    }
    $ar = array_unique($data, SORT_REGULAR);

    foreach ($ar as $key => $value) {

        $res = get_fields($value['post_id']);

        $id_group = preg_replace('#[[:punct:], \' \']#', '',  mb_strtolower(transliterate($res['name_snt'])));

        if (empty($res['id_group'])) {
            $array[] = [
                'name_snt' => $res['name_snt'],
                'post_id' => $value['post_id'],
                'id_group' => $id_group,
            ];
            update_field('id_group', $id_group, $value['post_id']);
        }
    }
    return $array;
}

function getDataByCity($city)
{
    foreach (getMunicipality() as $key => $value) {
        if ($key === $city) {
            $data[] = $value;
        }
    }
    if (!empty($data)) {
        return $data;
    }
    return null;
}

function getMunicipalityById($id_municipality)
{
    foreach (getMunicipality() as $key => $value) {
        foreach ($value as $k => $v) {
            if ($v['id_municipality'] === $id_municipality) {
                $data = [
                    'city' => $v['city'],
                    'municipality' => $v['municipality'],
                    'id_municipality' => $v['id_municipality'],
                    'map_svg' => $v['map_svg'],
                    'square' => $v['square'],
                    'population' => $v['population'],
                    'land_plots' => $v['land_plots'],
                    'number_snt' => $v['number_snt'],
                ];
            }
        }
    }
    if (!empty($data)) {
        return $data;
    }
    return null;
}

function getDataMunicipalityByIdJson($id_municipality)
{
    foreach (getMunicipality() as $key => $value) {
        foreach ($value as $k => $v) {
            if ($v['id_municipality'] === $id_municipality) {
                $data = [
                    'square' => $v['square'],
                    'population' => $v['population'],
                    'land_plots' => $v['land_plots'],
                    'number_snt' => $v['number_snt'],
                ];
            }
        }
    }
    if (!empty($data)) {
        return json_encode($data);
    }
    return null;
}

function getDataMunicipalityByName($municipality)
{
    foreach (getMunicipality() as $key => $value) {
        foreach ($value as $k => $v) {
            if ($v['municipality'] === $municipality) {
                $data = [
                    'municipality' => $v['municipality'],
                    'id_municipality' => $v['id_municipality'],
                    'square' => $v['square'],
                    'population' => $v['population'],
                    'land_plots' => $v['land_plots'],
                    'number_snt' => $v['number_snt'],
                ];
            }
        }
    }
    if (!empty($data)) {
        return $data;
    }
    return null;
}

function getDataMunicipalityByCity($city)
{
    foreach (getMunicipality() as $key => $value) {
        foreach ($value as $k => $v) {
            if ($v['city'] === $city) {
                $data[] = [
                    'municipality' => $v['municipality'],
                    'id_municipality' => $v['id_municipality'],
                    'square' => $v['square'],
                    'population' => $v['population'],
                    'land_plots' => $v['land_plots'],
                    'number_snt' => $v['number_snt'],
                ];
            }
        }
    }
    if (!empty($data)) {
        return $data;
    }
    return null;
}

/*
 * ******** Ресурсы регионов ***********
 */

function log_to_console($data)
{
    $output = json_encode($data, JSON_HEX_TAG);

    echo "<script>console.log('$output' );</script>";
}


function getResource()
{

    $fields = get_fields('option');
    foreach ($fields['resources'] as $key => $value) {
        $result[] = [
            'id' => $value['id_resource'],
            'name' => $value['name_resources'],
            'url' => $value['link_url'],
        ];
    }
    return $result;
}

function getResourceById($ID)
{
    foreach (getResource() as $key => $value) {
        if ($value['id'] === $ID) {
            $result = [
                'id' => $value['id'],
                'name' => $value['name'],
                'url' => $value['url'],
            ];
        }
    }
    return json_encode($result);
}
