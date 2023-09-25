<?php

function add_pwa_headers()
{
    echo '<meta name="apple-mobile-web-app-capable" content="yes">';
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="default">';
    echo '<meta name="apple-mobile-web-app-title" content="' . get_bloginfo('name') . '">';
    echo '<meta name="mobile-web-app-capable" content="yes">';

    $logo = get_theme_mod('custom_logo');
    echo '<link rel="apple-touch-startup-image" href="' . wp_get_attachment_image_url($logo, array(640, 1136)) . '" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">';
    echo '<link rel="apple-touch-startup-image" href="' . wp_get_attachment_image_url($logo, array(750, 1294)) . '" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">';
    echo '<link rel="apple-touch-startup-image" href="' . wp_get_attachment_image_url($logo, array(1242, 2148)) . '" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">';
    echo '<link rel="apple-touch-startup-image" href="' . wp_get_attachment_image_url($logo, array(1125, 2436)) . '" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">';
    echo '<link rel="apple-touch-startup-image" href="' . wp_get_attachment_image_url($logo, array(1536, 2048)) . '" media="(min-device-width: 768px) and (max-device-width: 1024px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">';
    echo '<link rel="apple-touch-startup-image" href="' . wp_get_attachment_image_url($logo, array(1668, 2224)) . '" media="(min-device-width: 834px) and (max-device-width: 834px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">';
    echo '<link rel="apple-touch-startup-image" href="' . wp_get_attachment_image_url($logo, array(2048, 2732)) . '" media="(min-device-width: 1024px) and (max-device-width: 1024px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">';

    echo '<meta name="msapplication-TileColor" content="' . (!empty($_COOKIE['dt']) ? '#fff' : '#000') . '">';
    echo '<meta name="theme-color" content="' . (!empty($_COOKIE['dt']) ? '#244b5b' : '#2fc2b5') . '">';
}
add_filter('wp_head', 'add_pwa_headers');

/**
 * Displays the default mainest.json file content.
 *
 * @since 0.1.3
 * 
 */
function do_mainfest()
{
    if ($_SERVER["REQUEST_URI"] == '/manifest.json') {
        header('Content-Type: application/manifest+json; charset=utf-8');

        $root = get_site_url();
        $theme = get_template_directory_uri();

        $logo = get_theme_mod('custom_logo');
        $logo_96 = wp_get_attachment_image_url($logo, 'pwa');
        $logo_256 = wp_get_attachment_image_url($logo, 'mpwa');

        $manifest = array(
            '$schema'           => 'https://json.schemastore.org/web-manifest-combined.json',
            'id'                => $root . '/pwa-worker.js?v=' . wp_get_theme()->get('Version'),
            'gcm_sender_id'     => get_theme_mod('notification_project_number'),
            'name'              => get_bloginfo('name'),
            //'name'              => get_bloginfo('name') . wp_title('|', false) . ' — ' . get_bloginfo('description'),
            'short_name'        => get_bloginfo('name'),
            'start_url'         => $root,
            'display'           => 'standalone',
            'lang'              => 'ru',
            'background_color'  => '#e5f4f4',
            "theme_color"       => '#2fc2b5',
            'description'       => get_bloginfo('description'),
            'categories'        => array(
                'education', 'business', 'social', 'bitrix', 'amocrm'
            ),
            'orientation'       => 'portrait',
            'icons'             => array(
                array(
                    'src'   => $logo_96,
                    'sizes' => '96x96',
                    'type'  => 'image/png'
                ), array(
                    'src'   => $logo_256,
                    'sizes' => '256x256',
                    'type'  => 'image/png'
                )
            ),
            'screenshots' => array(
                array(
                    'src' => $theme . '/screenshot.png',
                    'sizes' => '1200x900',
                    'type' => 'image/png',
                    'platform' => 'wide',
                    'label' => 'SMPY from Space'
                )
            ),
            'shortcuts'  => array(
                array(
                    'name'          => 'Feed',
                    'url'           => '/feed',
                    'description'   => 'List of actual events for today',
                    'icons'         => array(
                        array(
                            'src'   => $logo_96,
                            'sizes' => '96x96',
                            'type'  => 'image/png'
                        ), array(
                            'src'   => $logo_256,
                            'sizes' => '256x256',
                            'type'  => 'image/png'
                        )
                    )
                ),
                array(
                    'name' => 'Resources',
                    'url' => '/resources',
                    'icons'         => array(
                        array(
                            'src'   => $logo_96,
                            'sizes' => '96x96',
                            'type'  => 'image/png'
                        ), array(
                            'src'   => $logo_256,
                            'sizes' => '256x256',
                            'type'  => 'image/png'
                        )
                    )
                ),
                array(
                    'name' => 'Projects',
                    'url' => '/projects',
                    'icons'         => array(
                        array(
                            'src'   => $logo_96,
                            'sizes' => '96x96',
                            'type'  => 'image/png'
                        ), array(
                            'src'   => $logo_256,
                            'sizes' => '256x256',
                            'type'  => 'image/png'
                        )
                    )
                ),
                array(
                    'name' => 'Jobs',
                    'url' => '/jobs',
                    'icons'         => array(
                        array(
                            'src'   => $logo_96,
                            'sizes' => '96x96',
                            'type'  => 'image/png'
                        ), array(
                            'src'   => $logo_256,
                            'sizes' => '256x256',
                            'type'  => 'image/png'
                        )
                    )
                ),
                array(
                    'name' => 'Community',
                    'url' => '/community',
                    'icons'         => array(
                        array(
                            'src'   => $logo_96,
                            'sizes' => '96x96',
                            'type'  => 'image/png'
                        ), array(
                            'src'   => $logo_256,
                            'sizes' => '256x256',
                            'type'  => 'image/png'
                        )
                    )
                )
            ),
            'protocol_handlers' => array(
                array(
                    'protocol' => 'mailto',
                    'url' => '/?mailto=%s'
                ),
                array(
                    'protocol' => 'web+contact',
                    'url' => '/?contact=%s'
                ),
                array(
                    'protocol' => 'web+channel',
                    'url' => '/?channel=%s'
                )
            ),
            'share_target' => array(
                'action' => admin_url('admin-ajax.php') . '?action=share_target',
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
                'params' => array(
                    'title' => 'name',
                    'text' => 'description',
                    'url' => 'link',
                    'files' => array(
                        array(
                            'name' => 'lists',
                            'accept' => array('text/csv', '.csv'),
                            'enctype' => 'multipart/form-data'
                        ),
                        array(
                            'name' => 'photos',
                            'accept' => array('image/png', '.png'),
                            'enctype' => 'multipart/form-data'
                        )
                    ),
                )
            ),
            'related_applications' => array(
                array(
                    'platform' => 'play',
                    'url' => 'https://play.google.com/store/apps/details?id=com.awmpuc'
                )
            )
        );

        $output = json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        //--------------------------------------
        header_remove('content-security-policy');
        header_remove('x-xss-protection');

        /**
         * Filters the mainest.json output.
         *
         * @since 0.1.3
         *
         * @param string $output The mainest.json output.
         */
        echo apply_filters('mainfest_json', $output);

        exit();
    }
}
add_action('parse_request', 'do_mainfest');

/**
 * Displays the default mainest.json file content.
 *
 * @since 0.1.3
 * 
 */
function do_pwaworkerjs()
{
    if ($_SERVER["REQUEST_URI"] == '/pwa-worker.js') {
        header('Content-Type: application/javascript; charset=utf-8');

        $theme = get_template_directory_uri();

        $logo = get_theme_mod('custom_logo');
        $logo_96 = wp_get_attachment_image_url($logo, 'pwa');
        $logo_256 = wp_get_attachment_image_url($logo, 'mpwa');

        $script = '
            var cacheName = "pwa-cache";
            
            // What to cache on worker init
            var urlsToCache = [

                "/",

                "' . $logo_96 . '",
                "' . $logo_256 . '",

                "' . $theme . '/assets/css/bootstrap.min.css",
                "' . $theme . '/assets/css/bootstrap.select.min.css",
                "' . $theme . '/assets/css/slick.css",
                "' . $theme . '/assets/css/main.css",

                "' . $theme . '/assets/js/lazysizes.min.js",
                "' . $theme . '/assets/js/bootstrap.min.js",
                "' . $theme . '/assets/js/bootstrap.select.min.js",
                "' . $theme . '/assets/js/slick.min.js",
                "' . $theme . '/assets/js/main.js",
                "' . $theme . '/assets/js/map.js",

                "' . $theme . '/screenshot.png"
            ];

            // Skip caching by regular expression
            var skipChache = [
                "wp-admin",
                "wp-login"
            ];

            self.addEventListener("install", function (event) {
                event.waitUntil(
                    caches.open(cacheName)
                        .then(function (cache) {
                            return cache.addAll(urlsToCache).catch(e=>console.error(e));
                        })
                );
            });

            self.addEventListener("fetch", function (event) {
                event.waitUntil(async function(){
                    if (event.request.method !== "POST") {
                        var exclude = new RegExp(skipChache.join("|"), "g");
                        if(escapeRegex(event.request.url).indexOf(exclude) !=-1){
                            const stored = await caches.match(event.request);
                            if (stored) 
                                return event.respondWith(stored);
                        }
                    }else{
                        const formData = await event.request.formData();
                        if(formData){
                            const email = formData.get("mailto");
                            if(email){
                                return event.respondWith(Response.redirect(
                                    "?action=contact_user&by_mail=" + email,
                                    303
                                ));
                            }
                        }
                    }

                    const response = await fetch(event.request);
                    const cache = await caches.open(cacheName);
                    cache.put(event.request, response.clone());
                    return event.respondWith(response);
                });
            });

            function escapeRegex(string) {
                return string.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
            }
        ';

        $script = preg_replace('/\n[ ]{12}/', '', $script);

        /**
         * Filters the mainest.json output.
         *
         * @since 0.1.3
         *
         * @param string $output The mainest.json output.
         */
        echo apply_filters('pwaworker_js', $script);

        exit();
    }
}
add_action('parse_request', 'do_pwaworkerjs');
