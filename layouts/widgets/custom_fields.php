<?php

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


            $municipality[$value['name_city']][] = [
                'city' => $value['name_city'],
                'municipality' => $value['municipality'],
                'id_municipality' => $id_municipality,
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
                        'snt' => $m['name_snt'],
                        'link_maps' => $m['link_maps'],
                    ];
                }
            } else {
                $res[] = [
                    'city' => $w['city'],
                    'municipality' => $w['municipality'],
                    'id_municipality' => $w['id_municipality'],
                    'snt' => '',
                    'link_maps' => '',
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

function getSntByMunicipality($municipality)
{
    foreach (getSntAll() as $key => $value) {
        if ($value['municipality'] === $municipality)
            $data[] = $value;
    }
    if (!empty($data)) {
        return $data;
    }
    return null;
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
