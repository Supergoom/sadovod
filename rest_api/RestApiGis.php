<?php


class RestApiGis
{

    protected function auth()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => URL_GIS . 'token/',
            CURLOPT_HTTPHEADER => [
                'Content-Type:application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"username":"' . USER_GIS . '", "password":"' . PASS_USER_GIS . '"}',
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $file = SERVER_PATH . 'auth/auth.json';
        file_put_contents($file, $response);

    }

    protected function refreshToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => URL_GIS . 'refresh/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            //CURLOPT_POSTFIELDS => json_encode($params, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type:application/json',
                'refresh: ' . $this->getRefreshUser(),
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    protected function getCurlPost($params, $method)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => URL_GIS . $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type:application/json',
                'token: ' . $this->getTokenUser(),
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    protected function getCurlGet($params, $method)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => URL_GIS . $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => ($params === false) ? '' : json_encode($params, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type:application/json',
                'token: ' . $this->getTokenUser(),
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    protected function getTokenUser()
    {
        $token_user = json_decode(file_get_contents(SERVER_PATH . 'auth/auth.json'), true);
        if ($token_user['access']) {
            return 'Bearer ' . $token_user['access'];
        }

        return false;
    }

    protected function getRefreshUser()
    {
        $token_user = json_decode(file_get_contents(SERVER_PATH . 'auth/auth.json'), true);
        if ($token_user['refresh']) {
            return $token_user['refresh'];
        }

        return false;
    }

    public function getGroupsGis() {

        $error = json_decode(file_get_contents(SERVER_PATH . 'error/error.json'), true);
        if (isset($error['code'])) {

            $this->auth();
            $file = SERVER_PATH . 'error/error.json';
            file_put_contents($file, '');
            $result = $this->getGroupsGis();

        } else {
            if ($this->getTokenUser()) {

                $result = $this->getCurlGet(false, 'user-groups/');

                if (isset($result["type"])) {

                    $json = json_encode($result);
                    $file = SERVER_PATH . 'error/error.json';
                    file_put_contents($file, $json);

                }
                if (isset($result["code"])) {

                    $this->auth();
                    $file = SERVER_PATH . 'error/error.json';
                    file_put_contents($file, '');
                    $result = $this->getGroupsGis();
                }
            }
        }
        return $result;
    }

    public function arrayDataUserToSendGis($ARRAY, $id_group) {
        $res = [
            "username"=> $ARRAY['login'],
            "password"=> $ARRAY['pass'],
            "groups"=> [$id_group],
            "is_active"=> false,
            "is_superuser"=> false,
            "is_staff"=> false,
            "snils"=> "",
            "appointment"=> "",
            "department"=> "",
            "email"=> $ARRAY['email'],
            "first_name"=> $ARRAY['first_name'],
            "last_name"=> $ARRAY['last_name'],
            "organization" => $ARRAY['namesnt'],
            "second_name"=> $ARRAY['patronymic'],
            "phone_number"=> $ARRAY['tel']
        ];
        return $res;
    }

    public function newUser($params)
    {
        $error = json_decode(file_get_contents(SERVER_PATH . 'error/error.json'), true);
        if (isset($error['code'])) {

            $this->auth();
            $file = SERVER_PATH . 'error/error.json';
            file_put_contents($file, '');
            $result = $this->newUser($params);

        } else {

            if ($this->getTokenUser()) {

                $result = $this->getCurlPost($params, 'users/');

                if (isset($result['id'])) {

                    $json = json_encode($result);
                    $file = SERVER_PATH . 'error/error.json';
                    file_put_contents($file, $json);
                    return $result;

                }
                if (isset($result["type"])) {

                    $json = json_encode($result);
                    $file = SERVER_PATH . 'error/error.json';
                    file_put_contents($file, $json);

                }
                if (isset($result["code"])) {

                    $this->auth();
                    $file = SERVER_PATH . 'error/error.json';
                    file_put_contents($file, '');
                    $result = $this->newUser($params);
                }
            }
        }

        return $result;
    }
}
