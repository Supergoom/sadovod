<?php

/**
 * Class GetUser
 */

class GetUser
{

    /**
     * @param $EMAIL
     * @return array
     */
    public function getUserByEmail($EMAIL)
    {

        $user['user'] = get_user_by('email', $EMAIL);
        $user['meta'] = get_user_meta($user['user']->ID);

        $user_data = [
            'id' => $user['user']->ID,
            'login' => $user['user']->data->user_login,
            'email' => $user['user']->data->user_email,
            'pass' => $user['user']->data->user_pass,
            'display_name' => $user['user']->data->display_name,
            'date_registered' => $user['user']->data->user_registered,
            'nicename' => $user['meta']['nickname'][0],
            'first_name' => $user['meta']['first_name'][0],
            'last_name' => $user['meta']['last_name'][0],
            'patronymic' => $user['meta']['patronymic'][0],
            'tel' => $user['meta']['tel'][0],
            'namesnt' => $user['meta']['namesnt'][0],
            'cadastral_num' => $user['meta']['cadastral_num'][0],
            'address' => $user['meta']['address'][0],

        ];

        return $user_data;
    }

    /**
     * @param $LOGIN
     * @return array
     */
    public function getUserByLogin($LOGIN)
    {

        $user['user'] = get_user_by('login', $LOGIN);
        $user['meta'] = get_user_meta($user['user']->ID);

        $user_data = [
            'id' => $user['user']->ID,
            'login' => $user['user']->data->user_login,
            'email' => $user['user']->data->user_email,
            'pass' => $user['user']->data->user_pass,
            'display_name' => $user['user']->data->display_name,
            'date_registered' => $user['user']->data->user_registered,
            'nicename' => $user['meta']['nickname'][0],
            'first_name' => $user['meta']['first_name'][0],
            'last_name' => $user['meta']['last_name'][0],
            'patronymic' => $user['meta']['patronymic'][0],
            'tel' => $user['meta']['tel'][0],
            'namesnt' => $user['meta']['namesnt'][0],
            'cadastral_num' => $user['meta']['cadastral_num'][0],
            'address' => $user['meta']['address'][0],

        ];

        return $user_data;
    }

    /**
     * @param $ID
     * @return array
     */
    public function getUserByID($ID, $PASS)
    {
        $user['meta'] = get_user_meta($ID);
        $user['user'] = get_user_by('ID', $ID);

        $user_data = [
            'id' => $user['user']->ID,
            'login' => $user['user']->data->user_login,
            'email' => $user['user']->data->user_email,
            'pass' => $PASS,
            'display_name' => $user['user']->data->display_name,
            'date_registered' => $user['user']->data->user_registered,
            'nicename' => $user['meta']['nickname'][0],
            'first_name' => $user['meta']['first_name'][0],
            'last_name' => $user['meta']['last_name'][0],
            'patronymic' => $user['meta']['patronymic'][0],
            'tel' => $user['meta']['tel'][0],
            'namesnt' => $user['meta']['namesnt'][0],
            'cadastral_num' => $user['meta']['cadastral_num'][0],
            'address' => $user['meta']['address'][0],
            'id_group' => preg_replace('#[[:punct:], \' \']#', '',  mb_strtolower(transliterate($user['meta']['namesnt'][0])))

        ];

        return $user_data;
    }
}

