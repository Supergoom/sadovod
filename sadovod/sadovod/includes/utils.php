<?php

/*  Обработка массива
-----------------------------------------------*/
function array_escape($array)
{
    global $wpdbx;

    foreach ($array as &$v) {
        if (is_array($v)) {
            $v = array_escape($v);
        } else {
            $v = $wpdbx->_real_escape($v);
        }
    }
    return $array;
}

/*  Вставить после ключа
-----------------------------------------------*/
function array_insert($array, $position, $insert)
{
    if (!is_array($array) || !is_array($insert) || !is_int($position))
        return false;

    while (isset($insert)) {
        $tmp = $array[$position] ?? null;
        $array[$position] = $insert;
        $insert = $tmp;
        $position++;
    }

    return $array;
}

/*  Удалить по значению
-----------------------------------------------*/
function array_remove_val($array, $value)
{
    if (!is_array($array) || (!is_int($value) && !is_string($value)))
        return false;

    $index = array_search($value, $array);
    if ($index >= 0) {
        array_splice($array, $index, 1);
    }

    return $array;
}

/*  Вставить после ключа
-----------------------------------------------*/
function array_splice_after_key($array, $key, $insert)
{
    if (!is_array($array) || !is_array($insert) || (!is_int($key) && !is_string($key)))
        return false;

    $key_pos = array_search($key, array_keys($array));
    if ($key_pos !== false) {
        $second_array = array_splice($array, ++$key_pos);
        $array = array_merge($array, $insert, $second_array);
    }

    return $array;
}

/*  Вставить перед ключом
-----------------------------------------------*/
function array_splice_before_key($array, $key, $insert)
{
    if (!is_array($array) || !is_array($insert) || (!is_int($key) && !is_string($key)))
        return false;

    $key_pos = array_search($key, array_keys($array));
    if ($key_pos !== false) {
        $second_array = array_splice($array, $key_pos);
        $array = array_merge($array, $insert, $second_array);
    }

    return $array;
}

/*  Вывести nonce
-----------------------------------------------*/
function sadovod_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $echo = true)
{
    $name        = esc_attr($name);
    $nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';

    if ($referer && !wp_doing_ajax()) {
        $nonce_field .= wp_referer_field(false);
    }

    if ($echo) {
        echo $nonce_field;
    }

    return $nonce_field;
}

/*  Получить лимиты размера файлов
-----------------------------------------------*/
function get_file_size_limits($type = null)
{
    $types = array(
        'image',
        'video',
        'audio',
        'document'
    );

    if ($type && $types[$type])
        $types = array($type);

    $sizes = array();
    foreach ($types as $option) {
        $value = get_theme_mod('max_' . $option . '_size');
        $sizes[$option] = absint($value) ? absint($value) : 1;
    }

    if (count($sizes) == 1)
        return apply_filters('file_size_limit', $sizes, $type);

    return apply_filters('file_size_limits', $sizes);
}

/*  Получить картинку поста
-----------------------------------------------*/
function get_the_post_thumbnail_uri($post = null, $size = 'post-thumbnail')
{
    $post_thumbnail_id = get_post_thumbnail_id($post);
    $thumbnail_url = wp_get_attachment_image_url($post_thumbnail_id, $size);

    return apply_filters('post_thumbnail_url', $thumbnail_url, $post, $size);
}

/*  Обернуть символом
-----------------------------------------------*/
function wrap_str($str, $sym = '"')
{
    if (empty($str))
        return $str;

    $prefix = substr($str, 0, 1) == $sym ? '' : $sym;
    $postfix = substr($str, -1, 1) == $sym ? '' : $sym;

    return $prefix . $str . $postfix;
}

/*  Преобразование в число
-----------------------------------------------*/
function parse_int($data = null, $positive = true)
{
    if (!isset($data))
        return 0;

    return $positive ? absint($data) : intval($data);
}

/*  Получить расширение
-----------------------------------------------*/
function get_file_extension($file)
{
    $n = strrpos($file, '.');
    return ($n === false) ? '' : substr($file, $n + 1);
}

/*  Преобразование чисел
-----------------------------------------------*/

function get_formated_number($number)
{
    $number = parse_int($number);
    return number_format($number, 0, '', ' ');
}

function get_redable_number($number)
{
    $names = array(
        0 => "",
        1 => __('k', 'oneunion-records'),
        2 => __('m', 'oneunion-records'),
        3 => __('b', 'oneunion-records'),
        4 => __('t', 'oneunion-records'),
    );

    $number = parse_int($number);

    $exp = 0;
    while ($number / pow(1000, $exp) >= 1000)
        $exp++;

    return round($number / pow(1000, $exp), 1) . " " . $names[$exp];
}

function get_relative_number($number, $relateTo)
{
    $number = parse_int($number);
    $relateTo = parse_int($relateTo);

    $exp = 0;
    while ($relateTo / pow(1000, $exp) >= 1000)
        $exp++;

    return round($number / pow(1000, $exp), 1);
}

/*  Функия определения типа контента
-----------------------------------------------*/
function ext_mime_content_type($filename)
{
    $mime_types = array(
        //text
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'webp' => 'image/webp',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'ogg' => 'audio/ogg',
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'mov' => 'video/webm',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'dot' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'xls' => 'application/vnd.ms-excel',
        'xlt' => 'application/vnd.ms-excel',
        'xla' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pot' => 'application/vnd.ms-powerpoint',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppa' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'mdb' => 'application/vnd.ms-access',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    $ext = $filename;
    if (strpos($ext, '.'))
        $ext = strtolower(end(explode('.', $filename)));

    if (isset($mime_types[$ext])) {
        return $mime_types[$ext];
    } elseif (is_file($filename) && function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);

        return $mimetype;
    } else {
        return 'application/octet-stream';
    }
}

/*  Обработать иконку
-----------------------------------------------*/
function get_site_icon($size = 512, $new_type = null)
{
    $site_icon_id = get_option('site_icon');

    $url = '';
    if ($site_icon_id) {
        if ($size >= 512) {
            $size_data = 'full';
        } else {
            $size_data = array($size, $size);
        }

        $url = wp_get_attachment_image_url($site_icon_id, $size_data);
        if ($new_type != null) {
            if ($new_type == 'ico') {
                $file = wp_get_original_image_path($site_icon_id);

                $cur_type = pathinfo($file, PATHINFO_EXTENSION);
                if ($cur_type !== $new_type) {
                    $output = substr($file, 0, -strlen($cur_type)) . $new_type;
                    if (!is_file($output) || (filemtime($output) + DAY_IN_SECONDS * 7) < current_time('U')) {
                        $ico_gen = new PHP_Ico($file, $size);
                    }

                    $url = url_from_file($output);
                }
            }
        }
    }

    return $url;
}

/*  Получить картинку
-----------------------------------------------*/
function get_page_image($new_type = null)
{
	global $post;

	$image = wp_get_attachment_image_url(get_theme_mod('page_placeholder'), 'snippet');
	if (isset($post) && !empty($post) && is_single()) {
		$attachment_id = get_post_thumbnail_id($post);
		if ($attachment_id)
			$image = wp_get_attachment_image_url($attachment_id, 'snippet');
	}

	if ($new_type != null) {
		$cur_type = pathinfo($image, PATHINFO_EXTENSION);
		if ($cur_type !== $new_type) {
			$file = file_from_url($image);
			$output = substr($file, 0, -strlen($cur_type)) . $new_type;

			if (!is_file($output) || (filemtime($output) + DAY_IN_SECONDS * 7) < current_time('U')) {
				$tmp = null;

				if (preg_match('/jpg|jpeg/i', $cur_type))
					$tmp = imagecreatefromjpeg($file);
				else if (preg_match('/png/i', $cur_type))
					$tmp = imagecreatefrompng($file);
				else if (preg_match('/gif/i', $cur_type))
					$tmp = imagecreatefromgif($file);
				else if (preg_match('/bmp/i', $cur_type))
					$tmp = imagecreatefrombmp($file);

				if (!empty($tmp)) {
					$created = false;
					if (preg_match('/jpg|jpeg/i', $new_type))
						$created = imagejpeg($tmp, $output, 100);
					else if (preg_match('/png/i', $new_type))
						$created = imagepng($tmp, $output, 100);
					else if (preg_match('/gif/i', $new_type))
						$created = imagegif($tmp, $output);
					else if (preg_match('/bmp/i', $new_type))
						$created = imagebmp($tmp, $output);

					if ($created) {
						$image = url_from_file($output);
					}

					imagedestroy($tmp);
				}
			}
		}
	}

	return $image;
}

function file_from_url($url)
{
    $dir = wp_get_upload_dir();
    return str_replace($dir['baseurl'], $dir['basedir'], $url);
}

function url_from_file($file)
{
    $dir = wp_get_upload_dir();
    return str_replace($dir['basedir'], $dir['baseurl'], $file);
}


function is_new_year()
{
	global $is_new_year;

	if (!isset($is_new_year)) {
		$cur_date = new DateTime();
		$cur_date = new DateTime($cur_date->format("0000-m-d"));

		$ny_start_date = new DateTime(get_theme_mod('new_year_start_date', '2000-12-15'));
		$ny_end_date = new DateTime(get_theme_mod('new_year_end_date', '2000-01-07'));
		$in_next_year = $ny_end_date < $ny_start_date;

		if ($in_next_year && $cur_date < $ny_end_date)
			$cur_date->add(new DateInterval("P1Y"));

		if ($cur_date > $ny_start_date) {
			if ($in_next_year)
				$ny_end_date->add(new DateInterval("P1Y"));

			if ($cur_date < $ny_end_date) {
				$is_new_year = true;
			}
		}
	}

	return $is_new_year = $is_new_year ?? false;
}

function get_current_logo_type($type)
{
    if (is_new_year())
        $type = $type . '_ny';

    return $type;
}

/*  Получить заглушку
-----------------------------------------------*/
function get_logo_placeholder($type)
{
    $fallback = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';

    $url = wp_get_attachment_image_url(get_theme_mod($type), 'tiny');
    if (empty($url))
        return $fallback;

    $path = file_from_url($url);
    if (empty($path))
        return $fallback;

    $storage = 'image_' . $type . '_base64';
    $base64 = get_transient($storage);
    $format = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (empty($base64)) {
        $image = null;

        if ($format == 'png') {
            $image = imagecreatefrompng($path);
            $image = optimize_image_palette($image);

            ob_start();
            imagepng($image, null, 9);
            $data = ob_get_clean();
        } else if (in_array($format, array('jpg', 'jpeg'))) {
            $image = imagecreatefromjpeg($path);
            $image = optimize_image_palette($image);

            ob_start();
            imagejpeg($image, null, 80);
            $data = ob_get_clean();
        } else {
            $data = file_get_contents($path);
        }

        $base64 = base64_encode($data);
        set_transient($storage, $base64, WEEK_IN_SECONDS);

        imagedestroy($image);
    }

    if (empty($base64))
        return $fallback;

    return 'data:image/' . $format . ';base64,' . $base64;
}

function optimize_image_palette($image)
{
    $width = imagesx($image);
    $height = imagesy($image);

    $palette = imagecreate($width, $height);
    imagecopy($palette, $image, 0, 0, 0, 0, $width, $height);

    imagedestroy($image);
    return $palette;
}

/*  Получить заголовки
-----------------------------------------------*/
function get_page_title()
{
    return get_bloginfo('name') . wp_title('|', false);
}

function get_page_description()
{
    global $post;

    $description = get_bloginfo('description');
    if (is_singular(array('post', 'projects', 'resources', 'jobs')) && isset($post)) {
        $description = get_the_excerpt($post->ID);
    }

    return $description;
}

/*  Получить имя цвета
-----------------------------------------------*/
function get_color_name($color)
{
    $color_name = '';
    if (!empty($color)) {
        global $color_cache;

        if (!isset($color_cache))
            $color_cache = array();

        if (isset($color_cache[$color]))
            return $color_cache[$color];

        global $color_table;
        if (!isset($color_table)) {
            #region $color_table array assigning 
            $color_table = array(
                __('Air blue', 'sadovod-colors') => array(93, 138, 168),
                __('Alice blue', 'sadovod-colors') => array(240, 248, 255),
                __('Alizarin crimson', 'sadovod-colors') => array(227, 38, 54),
                __('Almond', 'sadovod-colors') => array(239, 222, 205),
                __('Amaranth', 'sadovod-colors') => array(229, 43, 80),
                __('Amber', 'sadovod-colors') => array(255, 191, 0),
                __('American rose', 'sadovod-colors') => array(255, 3, 62),
                __('Amethyst', 'sadovod-colors') => array(153, 102, 204),
                __('Android Green', 'sadovod-colors') => array(164, 198, 57),
                __('Anti-flash white', 'sadovod-colors') => array(242, 243, 244),
                __('Antique brass', 'sadovod-colors') => array(205, 149, 117),
                __('Antique fuchsia', 'sadovod-colors') => array(145, 92, 131),
                __('Antique white', 'sadovod-colors') => array(250, 235, 215),
                __('Apple green', 'sadovod-colors') => array(141, 182, 0),
                __('Apricot', 'sadovod-colors') => array(251, 206, 177),
                __('Aqua', 'sadovod-colors') => array(0, 255, 255),
                __('Aquamarine', 'sadovod-colors') => array(127, 255, 212),
                __('Army green', 'sadovod-colors') => array(75, 83, 32),
                __('Arylide yellow', 'sadovod-colors') => array(233, 214, 107),
                __('Ash grey', 'sadovod-colors') => array(178, 190, 181),
                __('Asparagus', 'sadovod-colors') => array(135, 169, 107),
                __('Atomic tangerine', 'sadovod-colors') => array(255, 153, 102),
                __('Auburn', 'sadovod-colors') => array(165, 42, 42),
                __('Aureolin', 'sadovod-colors') => array(253, 238, 0),
                __('AuroMetalSaurus', 'sadovod-colors') => array(110, 127, 128),
                __('Awesome', 'sadovod-colors') => array(255, 32, 82),
                __('Azure', 'sadovod-colors') => array(0, 127, 255),
                __('Azure mist/web', 'sadovod-colors') => array(240, 255, 255),
                __('Baby blue', 'sadovod-colors') => array(137, 207, 240),
                __('Baby blue eyes', 'sadovod-colors') => array(161, 202, 241),
                __('Baby pink', 'sadovod-colors') => array(244, 194, 194),
                __('Ball Blue', 'sadovod-colors') => array(33, 171, 205),
                __('Banana Mania', 'sadovod-colors') => array(250, 231, 181),
                __('Banana yellow', 'sadovod-colors') => array(255, 225, 53),
                __('Battleship grey', 'sadovod-colors') => array(132, 132, 130),
                __('Bazaar', 'sadovod-colors') => array(152, 119, 123),
                __('Beau blue', 'sadovod-colors') => array(188, 212, 230),
                __('Beaver', 'sadovod-colors') => array(159, 129, 112),
                __('Beige', 'sadovod-colors') => array(245, 245, 220),
                __('Bisque', 'sadovod-colors') => array(255, 228, 196),
                __('Bistre', 'sadovod-colors') => array(61, 43, 31),
                __('Bittersweet', 'sadovod-colors') => array(254, 111, 94),
                __('Black', 'sadovod-colors') => array(0, 0, 0),
                __('Blanched Almond', 'sadovod-colors') => array(255, 235, 205),
                __('Bleu de France', 'sadovod-colors') => array(49, 140, 231),
                __('Blizzard Blue', 'sadovod-colors') => array(172, 229, 238),
                __('Blond', 'sadovod-colors') => array(250, 240, 190),
                __('Blue', 'sadovod-colors') => array(0, 0, 255),
                __('Blue Bell', 'sadovod-colors') => array(162, 162, 208),
                __('Blue Gray', 'sadovod-colors') => array(102, 153, 204),
                __('Blue green', 'sadovod-colors') => array(13, 152, 186),
                __('Blue purple', 'sadovod-colors') => array(138, 43, 226),
                __('Blue violet', 'sadovod-colors') => array(138, 43, 226),
                __('Blush', 'sadovod-colors') => array(222, 93, 131),
                __('Bole', 'sadovod-colors') => array(121, 68, 59),
                __('Bondi blue', 'sadovod-colors') => array(0, 149, 182),
                __('Bone', 'sadovod-colors') => array(227, 218, 201),
                __('Boston University Red', 'sadovod-colors') => array(204, 0, 0),
                __('Bottle green', 'sadovod-colors') => array(0, 106, 78),
                __('Boysenberry', 'sadovod-colors') => array(135, 50, 96),
                __('Brandeis blue', 'sadovod-colors') => array(0, 112, 255),
                __('Brass', 'sadovod-colors') => array(181, 166, 66),
                __('Brick red', 'sadovod-colors') => array(203, 65, 84),
                __('Bright cerulean', 'sadovod-colors') => array(29, 172, 214),
                __('Bright green', 'sadovod-colors') => array(102, 255, 0),
                __('Bright lavender', 'sadovod-colors') => array(191, 148, 228),
                __('Bright maroon', 'sadovod-colors') => array(195, 33, 72),
                __('Bright pink', 'sadovod-colors') => array(255, 0, 127),
                __('Bright turquoise', 'sadovod-colors') => array(8, 232, 222),
                __('Bright ube', 'sadovod-colors') => array(209, 159, 232),
                __('Brilliant lavender', 'sadovod-colors') => array(244, 187, 255),
                __('Brilliant rose', 'sadovod-colors') => array(255, 85, 163),
                __('Brink pink', 'sadovod-colors') => array(251, 96, 127),
                __('British racing green', 'sadovod-colors') => array(0, 66, 37),
                __('Bronze', 'sadovod-colors') => array(205, 127, 50),
                __('Brown', 'sadovod-colors') => array(165, 42, 42),
                __('Bubble gum', 'sadovod-colors') => array(255, 193, 204),
                __('Bubbles', 'sadovod-colors') => array(231, 254, 255),
                __('Buff', 'sadovod-colors') => array(240, 220, 130),
                __('Bulgarian rose', 'sadovod-colors') => array(72, 6, 7),
                __('Burgundy', 'sadovod-colors') => array(128, 0, 32),
                __('Burlywood', 'sadovod-colors') => array(222, 184, 135),
                __('Burnt orange', 'sadovod-colors') => array(204, 85, 0),
                __('Burnt sienna', 'sadovod-colors') => array(233, 116, 81),
                __('Burnt umber', 'sadovod-colors') => array(138, 51, 36),
                __('Byzantine', 'sadovod-colors') => array(189, 51, 164),
                __('Byzantium', 'sadovod-colors') => array(112, 41, 99),
                __('CG Blue', 'sadovod-colors') => array(0, 122, 165),
                __('CG Red', 'sadovod-colors') => array(224, 60, 49),
                __('Cadet', 'sadovod-colors') => array(83, 104, 114),
                __('Cadet blue', 'sadovod-colors') => array(95, 158, 160),
                __('Cadet grey', 'sadovod-colors') => array(145, 163, 176),
                __('Cadmium green', 'sadovod-colors') => array(0, 107, 60),
                __('Cadmium orange', 'sadovod-colors') => array(237, 135, 45),
                __('Cadmium red', 'sadovod-colors') => array(227, 0, 34),
                __('Cadmium yellow', 'sadovod-colors') => array(255, 246, 0),
                __('Café au lait', 'sadovod-colors') => array(166, 123, 91),
                __('Café noir', 'sadovod-colors') => array(75, 54, 33),
                __('Cal Poly Pomona green', 'sadovod-colors') => array(30, 77, 43),
                __('Cambridge Blue', 'sadovod-colors') => array(163, 193, 173),
                __('Camel', 'sadovod-colors') => array(193, 154, 107),
                __('Camouflage green', 'sadovod-colors') => array(120, 134, 107),
                __('Canary', 'sadovod-colors') => array(255, 255, 153),
                __('Canary yellow', 'sadovod-colors') => array(255, 239, 0),
                __('Candy apple red', 'sadovod-colors') => array(255, 8, 0),
                __('Candy pink', 'sadovod-colors') => array(228, 113, 122),
                __('Capri', 'sadovod-colors') => array(0, 191, 255),
                __('Caput mortuum', 'sadovod-colors') => array(89, 39, 32),
                __('Cardinal', 'sadovod-colors') => array(196, 30, 58),
                __('Caribbean green', 'sadovod-colors') => array(0, 204, 153),
                __('Carmine', 'sadovod-colors') => array(255, 0, 64),
                __('Carmine pink', 'sadovod-colors') => array(235, 76, 66),
                __('Carmine red', 'sadovod-colors') => array(255, 0, 56),
                __('Carnation pink', 'sadovod-colors') => array(255, 166, 201),
                __('Carnelian', 'sadovod-colors') => array(179, 27, 27),
                __('Carolina blue', 'sadovod-colors') => array(153, 186, 221),
                __('Carrot orange', 'sadovod-colors') => array(237, 145, 33),
                __('Celadon', 'sadovod-colors') => array(172, 225, 175),
                __('Celeste', 'sadovod-colors') => array(178, 255, 255),
                __('Celestial blue', 'sadovod-colors') => array(73, 151, 208),
                __('Cerise', 'sadovod-colors') => array(222, 49, 99),
                __('Cerise pink', 'sadovod-colors') => array(236, 59, 131),
                __('Cerulean', 'sadovod-colors') => array(0, 123, 167),
                __('Cerulean blue', 'sadovod-colors') => array(42, 82, 190),
                __('Chamoisee', 'sadovod-colors') => array(160, 120, 90),
                __('Champagne', 'sadovod-colors') => array(250, 214, 165),
                __('Charcoal', 'sadovod-colors') => array(54, 69, 79),
                __('Chartreuse', 'sadovod-colors') => array(127, 255, 0),
                __('Cherry', 'sadovod-colors') => array(222, 49, 99),
                __('Cherry blossom pink', 'sadovod-colors') => array(255, 183, 197),
                __('Chestnut', 'sadovod-colors') => array(205, 92, 92),
                __('Chocolate', 'sadovod-colors') => array(210, 105, 30),
                __('Chrome yellow', 'sadovod-colors') => array(255, 167, 0),
                __('Cinereous', 'sadovod-colors') => array(152, 129, 123),
                __('Cinnabar', 'sadovod-colors') => array(227, 66, 52),
                __('Cinnamon', 'sadovod-colors') => array(210, 105, 30),
                __('Citrine', 'sadovod-colors') => array(228, 208, 10),
                __('Classic rose', 'sadovod-colors') => array(251, 204, 231),
                __('Cobalt', 'sadovod-colors') => array(0, 71, 171),
                __('Cocoa brown', 'sadovod-colors') => array(210, 105, 30),
                __('Coffee', 'sadovod-colors') => array(111, 78, 55),
                __('Columbia blue', 'sadovod-colors') => array(155, 221, 255),
                __('Cool black', 'sadovod-colors') => array(0, 46, 99),
                __('Cool grey', 'sadovod-colors') => array(140, 146, 172),
                __('Copper', 'sadovod-colors') => array(184, 115, 51),
                __('Copper rose', 'sadovod-colors') => array(153, 102, 102),
                __('Coquelicot', 'sadovod-colors') => array(255, 56, 0),
                __('Coral', 'sadovod-colors') => array(255, 127, 80),
                __('Coral pink', 'sadovod-colors') => array(248, 131, 121),
                __('Coral red', 'sadovod-colors') => array(255, 64, 64),
                __('Cordovan', 'sadovod-colors') => array(137, 63, 69),
                __('Corn', 'sadovod-colors') => array(251, 236, 93),
                __('Cornell Red', 'sadovod-colors') => array(179, 27, 27),
                __('Cornflower', 'sadovod-colors') => array(154, 206, 235),
                __('Cornflower blue', 'sadovod-colors') => array(100, 149, 237),
                __('Cornsilk', 'sadovod-colors') => array(255, 248, 220),
                __('Cosmic latte', 'sadovod-colors') => array(255, 248, 231),
                __('Cotton candy', 'sadovod-colors') => array(255, 188, 217),
                __('Cream', 'sadovod-colors') => array(255, 253, 208),
                __('Crimson', 'sadovod-colors') => array(220, 20, 60),
                __('Crimson Red', 'sadovod-colors') => array(153, 0, 0),
                __('Crimson glory', 'sadovod-colors') => array(190, 0, 50),
                __('Cyan', 'sadovod-colors') => array(0, 255, 255),
                __('Daffodil', 'sadovod-colors') => array(255, 255, 49),
                __('Dandelion', 'sadovod-colors') => array(240, 225, 48),
                __('Dark blue', 'sadovod-colors') => array(0, 0, 139),
                __('Dark brown', 'sadovod-colors') => array(101, 67, 33),
                __('Dark byzantium', 'sadovod-colors') => array(93, 57, 84),
                __('Dark candy apple red', 'sadovod-colors') => array(164, 0, 0),
                __('Dark cerulean', 'sadovod-colors') => array(8, 69, 126),
                __('Dark chestnut', 'sadovod-colors') => array(152, 105, 96),
                __('Dark coral', 'sadovod-colors') => array(205, 91, 69),
                __('Dark cyan', 'sadovod-colors') => array(0, 139, 139),
                __('Dark electric blue', 'sadovod-colors') => array(83, 104, 120),
                __('Dark goldenrod', 'sadovod-colors') => array(184, 134, 11),
                __('Dark gray', 'sadovod-colors') => array(169, 169, 169),
                __('Dark green', 'sadovod-colors') => array(1, 50, 32),
                __('Dark jungle green', 'sadovod-colors') => array(26, 36, 33),
                __('Dark khaki', 'sadovod-colors') => array(189, 183, 107),
                __('Dark lava', 'sadovod-colors') => array(72, 60, 50),
                __('Dark lavender', 'sadovod-colors') => array(115, 79, 150),
                __('Dark magenta', 'sadovod-colors') => array(139, 0, 139),
                __('Dark midnight blue', 'sadovod-colors') => array(0, 51, 102),
                __('Dark olive green', 'sadovod-colors') => array(85, 107, 47),
                __('Dark orange', 'sadovod-colors') => array(255, 140, 0),
                __('Dark orchid', 'sadovod-colors') => array(153, 50, 204),
                __('Dark pastel blue', 'sadovod-colors') => array(119, 158, 203),
                __('Dark pastel green', 'sadovod-colors') => array(3, 192, 60),
                __('Dark pastel purple', 'sadovod-colors') => array(150, 111, 214),
                __('Dark pastel red', 'sadovod-colors') => array(194, 59, 34),
                __('Dark pink', 'sadovod-colors') => array(231, 84, 128),
                __('Dark powder blue', 'sadovod-colors') => array(0, 51, 153),
                __('Dark raspberry', 'sadovod-colors') => array(135, 38, 87),
                __('Dark red', 'sadovod-colors') => array(139, 0, 0),
                __('Dark salmon', 'sadovod-colors') => array(233, 150, 122),
                __('Dark scarlet', 'sadovod-colors') => array(86, 3, 25),
                __('Dark sea green', 'sadovod-colors') => array(143, 188, 143),
                __('Dark sienna', 'sadovod-colors') => array(60, 20, 20),
                __('Dark slate blue', 'sadovod-colors') => array(72, 61, 139),
                __('Dark slate gray', 'sadovod-colors') => array(47, 79, 79),
                __('Dark spring green', 'sadovod-colors') => array(23, 114, 69),
                __('Dark tan', 'sadovod-colors') => array(145, 129, 81),
                __('Dark tangerine', 'sadovod-colors') => array(255, 168, 18),
                __('Dark taupe', 'sadovod-colors') => array(72, 60, 50),
                __('Dark terra cotta', 'sadovod-colors') => array(204, 78, 92),
                __('Dark turquoise', 'sadovod-colors') => array(0, 206, 209),
                __('Dark violet', 'sadovod-colors') => array(148, 0, 211),
                __('Dartmouth green', 'sadovod-colors') => array(0, 105, 62),
                __('Davy grey', 'sadovod-colors') => array(85, 85, 85),
                __('Debian red', 'sadovod-colors') => array(215, 10, 83),
                __('Deep carmine', 'sadovod-colors') => array(169, 32, 62),
                __('Deep carmine pink', 'sadovod-colors') => array(239, 48, 56),
                __('Deep carrot orange', 'sadovod-colors') => array(233, 105, 44),
                __('Deep cerise', 'sadovod-colors') => array(218, 50, 135),
                __('Deep champagne', 'sadovod-colors') => array(250, 214, 165),
                __('Deep chestnut', 'sadovod-colors') => array(185, 78, 72),
                __('Deep coffee', 'sadovod-colors') => array(112, 66, 65),
                __('Deep fuchsia', 'sadovod-colors') => array(193, 84, 193),
                __('Deep jungle green', 'sadovod-colors') => array(0, 75, 73),
                __('Deep lilac', 'sadovod-colors') => array(153, 85, 187),
                __('Deep magenta', 'sadovod-colors') => array(204, 0, 204),
                __('Deep peach', 'sadovod-colors') => array(255, 203, 164),
                __('Deep pink', 'sadovod-colors') => array(255, 20, 147),
                __('Deep saffron', 'sadovod-colors') => array(255, 153, 51),
                __('Deep sky blue', 'sadovod-colors') => array(0, 191, 255),
                __('Denim', 'sadovod-colors') => array(21, 96, 189),
                __('Desert', 'sadovod-colors') => array(193, 154, 107),
                __('Desert sand', 'sadovod-colors') => array(237, 201, 175),
                __('Dim gray', 'sadovod-colors') => array(105, 105, 105),
                __('Dodger blue', 'sadovod-colors') => array(30, 144, 255),
                __('Dogwood rose', 'sadovod-colors') => array(215, 24, 104),
                __('Dollar bill', 'sadovod-colors') => array(133, 187, 101),
                __('Drab', 'sadovod-colors') => array(150, 113, 23),
                __('Duke blue', 'sadovod-colors') => array(0, 0, 156),
                __('Earth yellow', 'sadovod-colors') => array(225, 169, 95),
                __('Ecru', 'sadovod-colors') => array(194, 178, 128),
                __('Eggplant', 'sadovod-colors') => array(97, 64, 81),
                __('Eggshell', 'sadovod-colors') => array(240, 234, 214),
                __('Egyptian blue', 'sadovod-colors') => array(16, 52, 166),
                __('Electric blue', 'sadovod-colors') => array(125, 249, 255),
                __('Electric crimson', 'sadovod-colors') => array(255, 0, 63),
                __('Electric cyan', 'sadovod-colors') => array(0, 255, 255),
                __('Electric green', 'sadovod-colors') => array(0, 255, 0),
                __('Electric indigo', 'sadovod-colors') => array(111, 0, 255),
                __('Electric lavender', 'sadovod-colors') => array(244, 187, 255),
                __('Electric lime', 'sadovod-colors') => array(204, 255, 0),
                __('Electric purple', 'sadovod-colors') => array(191, 0, 255),
                __('Electric ultramarine', 'sadovod-colors') => array(63, 0, 255),
                __('Electric violet', 'sadovod-colors') => array(143, 0, 255),
                __('Electric yellow', 'sadovod-colors') => array(255, 255, 0),
                __('Emerald', 'sadovod-colors') => array(80, 200, 120),
                __('Eton blue', 'sadovod-colors') => array(150, 200, 162),
                __('Fallow', 'sadovod-colors') => array(193, 154, 107),
                __('Falu red', 'sadovod-colors') => array(128, 24, 24),
                __('Famous', 'sadovod-colors') => array(255, 0, 255),
                __('Fandango', 'sadovod-colors') => array(181, 51, 137),
                __('Fashion fuchsia', 'sadovod-colors') => array(244, 0, 161),
                __('Fawn', 'sadovod-colors') => array(229, 170, 112),
                __('Feldgrau', 'sadovod-colors') => array(77, 93, 83),
                __('Fern', 'sadovod-colors') => array(113, 188, 120),
                __('Fern green', 'sadovod-colors') => array(79, 121, 66),
                __('Ferrari Red', 'sadovod-colors') => array(255, 40, 0),
                __('Field drab', 'sadovod-colors') => array(108, 84, 30),
                __('Fire engine red', 'sadovod-colors') => array(206, 32, 41),
                __('Firebrick', 'sadovod-colors') => array(178, 34, 34),
                __('Flame', 'sadovod-colors') => array(226, 88, 34),
                __('Flamingo pink', 'sadovod-colors') => array(252, 142, 172),
                __('Flavescent', 'sadovod-colors') => array(247, 233, 142),
                __('Flax', 'sadovod-colors') => array(238, 220, 130),
                __('Floral white', 'sadovod-colors') => array(255, 250, 240),
                __('Fluorescent orange', 'sadovod-colors') => array(255, 191, 0),
                __('Fluorescent pink', 'sadovod-colors') => array(255, 20, 147),
                __('Fluorescent yellow', 'sadovod-colors') => array(204, 255, 0),
                __('Folly', 'sadovod-colors') => array(255, 0, 79),
                __('Forest green', 'sadovod-colors') => array(34, 139, 34),
                __('French beige', 'sadovod-colors') => array(166, 123, 91),
                __('French blue', 'sadovod-colors') => array(0, 114, 187),
                __('French lilac', 'sadovod-colors') => array(134, 96, 142),
                __('French rose', 'sadovod-colors') => array(246, 74, 138),
                __('Fuchsia', 'sadovod-colors') => array(255, 0, 255),
                __('Fuchsia pink', 'sadovod-colors') => array(255, 119, 255),
                __('Fulvous', 'sadovod-colors') => array(228, 132, 0),
                __('Fuzzy Wuzzy', 'sadovod-colors') => array(204, 102, 102),
                __('Gainsboro', 'sadovod-colors') => array(220, 220, 220),
                __('Gamboge', 'sadovod-colors') => array(228, 155, 15),
                __('Ghost white', 'sadovod-colors') => array(248, 248, 255),
                __('Ginger', 'sadovod-colors') => array(176, 101, 0),
                __('Glaucous', 'sadovod-colors') => array(96, 130, 182),
                __('Glitter', 'sadovod-colors') => array(230, 232, 250),
                __('Gold', 'sadovod-colors') => array(255, 215, 0),
                __('Golden brown', 'sadovod-colors') => array(153, 101, 21),
                __('Golden poppy', 'sadovod-colors') => array(252, 194, 0),
                __('Golden yellow', 'sadovod-colors') => array(255, 223, 0),
                __('Goldenrod', 'sadovod-colors') => array(218, 165, 32),
                __('Granny Smith Apple', 'sadovod-colors') => array(168, 228, 160),
                __('Gray', 'sadovod-colors') => array(128, 128, 128),
                __('Gray asparagus', 'sadovod-colors') => array(70, 89, 69),
                __('Green', 'sadovod-colors') => array(0, 255, 0),
                __('Green Blue', 'sadovod-colors') => array(17, 100, 180),
                __('Green yellow', 'sadovod-colors') => array(173, 255, 47),
                __('Grullo', 'sadovod-colors') => array(169, 154, 134),
                __('Guppie green', 'sadovod-colors') => array(0, 255, 127),
                __('Halayà úbe', 'sadovod-colors') => array(102, 56, 84),
                __('Han blue', 'sadovod-colors') => array(68, 108, 207),
                __('Han purple', 'sadovod-colors') => array(82, 24, 250),
                __('Hansa yellow', 'sadovod-colors') => array(233, 214, 107),
                __('Harlequin', 'sadovod-colors') => array(63, 255, 0),
                __('Harvard crimson', 'sadovod-colors') => array(201, 0, 22),
                __('Harvest Gold', 'sadovod-colors') => array(218, 145, 0),
                __('Heart Gold', 'sadovod-colors') => array(128, 128, 0),
                __('Heliotrope', 'sadovod-colors') => array(223, 115, 255),
                __('Hollywood cerise', 'sadovod-colors') => array(244, 0, 161),
                __('Honeydew', 'sadovod-colors') => array(240, 255, 240),
                __('Hooker green', 'sadovod-colors') => array(73, 121, 107),
                __('Hot magenta', 'sadovod-colors') => array(255, 29, 206),
                __('Hot pink', 'sadovod-colors') => array(255, 105, 180),
                __('Hunter green', 'sadovod-colors') => array(53, 94, 59),
                __('Icterine', 'sadovod-colors') => array(252, 247, 94),
                __('Inchworm', 'sadovod-colors') => array(178, 236, 93),
                __('India green', 'sadovod-colors') => array(19, 136, 8),
                __('Indian red', 'sadovod-colors') => array(205, 92, 92),
                __('Indian yellow', 'sadovod-colors') => array(227, 168, 87),
                __('Indigo', 'sadovod-colors') => array(75, 0, 130),
                __('International Klein Blue', 'sadovod-colors') => array(0, 47, 167),
                __('International orange', 'sadovod-colors') => array(255, 79, 0),
                __('Iris', 'sadovod-colors') => array(90, 79, 207),
                __('Isabelline', 'sadovod-colors') => array(244, 240, 236),
                __('Islamic green', 'sadovod-colors') => array(0, 144, 0),
                __('Ivory', 'sadovod-colors') => array(255, 255, 240),
                __('Jade', 'sadovod-colors') => array(0, 168, 107),
                __('Jasmine', 'sadovod-colors') => array(248, 222, 126),
                __('Jasper', 'sadovod-colors') => array(215, 59, 62),
                __('Jazzberry jam', 'sadovod-colors') => array(165, 11, 94),
                __('Jonquil', 'sadovod-colors') => array(250, 218, 94),
                __('June bud', 'sadovod-colors') => array(189, 218, 87),
                __('Jungle green', 'sadovod-colors') => array(41, 171, 135),
                __('KU Crimson', 'sadovod-colors') => array(232, 0, 13),
                __('Kelly green', 'sadovod-colors') => array(76, 187, 23),
                __('Khaki', 'sadovod-colors') => array(195, 176, 145),
                __('La Salle Green', 'sadovod-colors') => array(8, 120, 48),
                __('Languid lavender', 'sadovod-colors') => array(214, 202, 221),
                __('Lapis lazuli', 'sadovod-colors') => array(38, 97, 156),
                __('Laser Lemon', 'sadovod-colors') => array(254, 254, 34),
                __('Laurel green', 'sadovod-colors') => array(169, 186, 157),
                __('Lava', 'sadovod-colors') => array(207, 16, 32),
                __('Lavender', 'sadovod-colors') => array(230, 230, 250),
                __('Lavender blue', 'sadovod-colors') => array(204, 204, 255),
                __('Lavender blush', 'sadovod-colors') => array(255, 240, 245),
                __('Lavender gray', 'sadovod-colors') => array(196, 195, 208),
                __('Lavender indigo', 'sadovod-colors') => array(148, 87, 235),
                __('Lavender magenta', 'sadovod-colors') => array(238, 130, 238),
                __('Lavender mist', 'sadovod-colors') => array(230, 230, 250),
                __('Lavender pink', 'sadovod-colors') => array(251, 174, 210),
                __('Lavender purple', 'sadovod-colors') => array(150, 123, 182),
                __('Lavender rose', 'sadovod-colors') => array(251, 160, 227),
                __('Lawn green', 'sadovod-colors') => array(124, 252, 0),
                __('Lemon', 'sadovod-colors') => array(255, 247, 0),
                __('Lemon Yellow', 'sadovod-colors') => array(255, 244, 79),
                __('Lemon chiffon', 'sadovod-colors') => array(255, 250, 205),
                __('Lemon lime', 'sadovod-colors') => array(191, 255, 0),
                __('Light Crimson', 'sadovod-colors') => array(245, 105, 145),
                __('Light Thulian pink', 'sadovod-colors') => array(230, 143, 172),
                __('Light apricot', 'sadovod-colors') => array(253, 213, 177),
                __('Light blue', 'sadovod-colors') => array(173, 216, 230),
                __('Light brown', 'sadovod-colors') => array(181, 101, 29),
                __('Light carmine pink', 'sadovod-colors') => array(230, 103, 113),
                __('Light coral', 'sadovod-colors') => array(240, 128, 128),
                __('Light cornflower blue', 'sadovod-colors') => array(147, 204, 234),
                __('Light cyan', 'sadovod-colors') => array(224, 255, 255),
                __('Light fuchsia pink', 'sadovod-colors') => array(249, 132, 239),
                __('Light goldenrod yellow', 'sadovod-colors') => array(250, 250, 210),
                __('Light gray', 'sadovod-colors') => array(211, 211, 211),
                __('Light green', 'sadovod-colors') => array(144, 238, 144),
                __('Light khaki', 'sadovod-colors') => array(240, 230, 140),
                __('Light pastel purple', 'sadovod-colors') => array(177, 156, 217),
                __('Light pink', 'sadovod-colors') => array(255, 182, 193),
                __('Light salmon', 'sadovod-colors') => array(255, 160, 122),
                __('Light salmon pink', 'sadovod-colors') => array(255, 153, 153),
                __('Light sea green', 'sadovod-colors') => array(32, 178, 170),
                __('Light sky blue', 'sadovod-colors') => array(135, 206, 250),
                __('Light slate gray', 'sadovod-colors') => array(119, 136, 153),
                __('Light taupe', 'sadovod-colors') => array(179, 139, 109),
                __('Light yellow', 'sadovod-colors') => array(255, 255, 237),
                __('Lilac', 'sadovod-colors') => array(200, 162, 200),
                __('Lime', 'sadovod-colors') => array(191, 255, 0),
                __('Lime green', 'sadovod-colors') => array(50, 205, 50),
                __('Lincoln green', 'sadovod-colors') => array(25, 89, 5),
                __('Linen', 'sadovod-colors') => array(250, 240, 230),
                __('Lion', 'sadovod-colors') => array(193, 154, 107),
                __('Liver', 'sadovod-colors') => array(83, 75, 79),
                __('Lust', 'sadovod-colors') => array(230, 32, 32),
                __('MSU Green', 'sadovod-colors') => array(24, 69, 59),
                __('Macaroni and Cheese', 'sadovod-colors') => array(255, 189, 136),
                __('Magenta', 'sadovod-colors') => array(255, 0, 255),
                __('Magic mint', 'sadovod-colors') => array(170, 240, 209),
                __('Magnolia', 'sadovod-colors') => array(248, 244, 255),
                __('Mahogany', 'sadovod-colors') => array(192, 64, 0),
                __('Maize', 'sadovod-colors') => array(251, 236, 93),
                __('Majorelle Blue', 'sadovod-colors') => array(96, 80, 220),
                __('Malachite', 'sadovod-colors') => array(11, 218, 81),
                __('Manatee', 'sadovod-colors') => array(151, 154, 170),
                __('Mango Tango', 'sadovod-colors') => array(255, 130, 67),
                __('Mantis', 'sadovod-colors') => array(116, 195, 101),
                __('Maroon', 'sadovod-colors') => array(128, 0, 0),
                __('Mauve', 'sadovod-colors') => array(224, 176, 255),
                __('Mauve taupe', 'sadovod-colors') => array(145, 95, 109),
                __('Mauvelous', 'sadovod-colors') => array(239, 152, 170),
                __('Maya blue', 'sadovod-colors') => array(115, 194, 251),
                __('Meat brown', 'sadovod-colors') => array(229, 183, 59),
                __('Medium Persian blue', 'sadovod-colors') => array(0, 103, 165),
                __('Medium aquamarine', 'sadovod-colors') => array(102, 221, 170),
                __('Medium blue', 'sadovod-colors') => array(0, 0, 205),
                __('Medium candy apple red', 'sadovod-colors') => array(226, 6, 44),
                __('Medium carmine', 'sadovod-colors') => array(175, 64, 53),
                __('Medium champagne', 'sadovod-colors') => array(243, 229, 171),
                __('Medium electric blue', 'sadovod-colors') => array(3, 80, 150),
                __('Medium jungle green', 'sadovod-colors') => array(28, 53, 45),
                __('Medium lavender magenta', 'sadovod-colors') => array(221, 160, 221),
                __('Medium orchid', 'sadovod-colors') => array(186, 85, 211),
                __('Medium purple', 'sadovod-colors') => array(147, 112, 219),
                __('Medium red violet', 'sadovod-colors') => array(187, 51, 133),
                __('Medium sea green', 'sadovod-colors') => array(60, 179, 113),
                __('Medium slate blue', 'sadovod-colors') => array(123, 104, 238),
                __('Medium spring bud', 'sadovod-colors') => array(201, 220, 135),
                __('Medium spring green', 'sadovod-colors') => array(0, 250, 154),
                __('Medium taupe', 'sadovod-colors') => array(103, 76, 71),
                __('Medium teal blue', 'sadovod-colors') => array(0, 84, 180),
                __('Medium turquoise', 'sadovod-colors') => array(72, 209, 204),
                __('Medium violet red', 'sadovod-colors') => array(199, 21, 133),
                __('Melon', 'sadovod-colors') => array(253, 188, 180),
                __('Midnight blue', 'sadovod-colors') => array(25, 25, 112),
                __('Midnight green', 'sadovod-colors') => array(0, 73, 83),
                __('Mikado yellow', 'sadovod-colors') => array(255, 196, 12),
                __('Mint', 'sadovod-colors') => array(62, 180, 137),
                __('Mint cream', 'sadovod-colors') => array(245, 255, 250),
                __('Mint green', 'sadovod-colors') => array(152, 255, 152),
                __('Misty rose', 'sadovod-colors') => array(255, 228, 225),
                __('Moccasin', 'sadovod-colors') => array(250, 235, 215),
                __('Mode beige', 'sadovod-colors') => array(150, 113, 23),
                __('Moonstone blue', 'sadovod-colors') => array(115, 169, 194),
                __('Mordant red 19', 'sadovod-colors') => array(174, 12, 0),
                __('Moss green', 'sadovod-colors') => array(173, 223, 173),
                __('Mountain Meadow', 'sadovod-colors') => array(48, 186, 143),
                __('Mountbatten pink', 'sadovod-colors') => array(153, 122, 141),
                __('Mulberry', 'sadovod-colors') => array(197, 75, 140),
                __('Munsell', 'sadovod-colors') => array(242, 243, 244),
                __('Mustard', 'sadovod-colors') => array(255, 219, 88),
                __('Myrtle', 'sadovod-colors') => array(33, 66, 30),
                __('Nadeshiko pink', 'sadovod-colors') => array(246, 173, 198),
                __('Napier green', 'sadovod-colors') => array(42, 128, 0),
                __('Naples yellow', 'sadovod-colors') => array(250, 218, 94),
                __('Navajo white', 'sadovod-colors') => array(255, 222, 173),
                __('Navy blue', 'sadovod-colors') => array(0, 0, 128),
                __('Neon Carrot', 'sadovod-colors') => array(255, 163, 67),
                __('Neon fuchsia', 'sadovod-colors') => array(254, 89, 194),
                __('Neon green', 'sadovod-colors') => array(57, 255, 20),
                __('Non-photo blue', 'sadovod-colors') => array(164, 221, 237),
                __('North Texas Green', 'sadovod-colors') => array(5, 144, 51),
                __('Ocean Boat Blue', 'sadovod-colors') => array(0, 119, 190),
                __('Ochre', 'sadovod-colors') => array(204, 119, 34),
                __('Office green', 'sadovod-colors') => array(0, 128, 0),
                __('Old gold', 'sadovod-colors') => array(207, 181, 59),
                __('Old lace', 'sadovod-colors') => array(253, 245, 230),
                __('Old lavender', 'sadovod-colors') => array(121, 104, 120),
                __('Old mauve', 'sadovod-colors') => array(103, 49, 71),
                __('Old rose', 'sadovod-colors') => array(192, 128, 129),
                __('Olive', 'sadovod-colors') => array(128, 128, 0),
                __('Olive Drab', 'sadovod-colors') => array(107, 142, 35),
                __('Olive Green', 'sadovod-colors') => array(186, 184, 108),
                __('Olivine', 'sadovod-colors') => array(154, 185, 115),
                __('Onyx', 'sadovod-colors') => array(15, 15, 15),
                __('Opera mauve', 'sadovod-colors') => array(183, 132, 167),
                __('Orange', 'sadovod-colors') => array(255, 165, 0),
                __('Orange Yellow', 'sadovod-colors') => array(248, 213, 104),
                __('Orange peel', 'sadovod-colors') => array(255, 159, 0),
                __('Orange red', 'sadovod-colors') => array(255, 69, 0),
                __('Orchid', 'sadovod-colors') => array(218, 112, 214),
                __('Otter brown', 'sadovod-colors') => array(101, 67, 33),
                __('Outer Space', 'sadovod-colors') => array(65, 74, 76),
                __('Outrageous Orange', 'sadovod-colors') => array(255, 110, 74),
                __('Oxford Blue', 'sadovod-colors') => array(0, 33, 71),
                __('Pacific Blue', 'sadovod-colors') => array(28, 169, 201),
                __('Pakistan green', 'sadovod-colors') => array(0, 102, 0),
                __('Palatinate blue', 'sadovod-colors') => array(39, 59, 226),
                __('Palatinate purple', 'sadovod-colors') => array(104, 40, 96),
                __('Pale aqua', 'sadovod-colors') => array(188, 212, 230),
                __('Pale blue', 'sadovod-colors') => array(175, 238, 238),
                __('Pale brown', 'sadovod-colors') => array(152, 118, 84),
                __('Pale carmine', 'sadovod-colors') => array(175, 64, 53),
                __('Pale cerulean', 'sadovod-colors') => array(155, 196, 226),
                __('Pale chestnut', 'sadovod-colors') => array(221, 173, 175),
                __('Pale copper', 'sadovod-colors') => array(218, 138, 103),
                __('Pale cornflower blue', 'sadovod-colors') => array(171, 205, 239),
                __('Pale gold', 'sadovod-colors') => array(230, 190, 138),
                __('Pale goldenrod', 'sadovod-colors') => array(238, 232, 170),
                __('Pale green', 'sadovod-colors') => array(152, 251, 152),
                __('Pale lavender', 'sadovod-colors') => array(220, 208, 255),
                __('Pale magenta', 'sadovod-colors') => array(249, 132, 229),
                __('Pale pink', 'sadovod-colors') => array(250, 218, 221),
                __('Pale plum', 'sadovod-colors') => array(221, 160, 221),
                __('Pale red violet', 'sadovod-colors') => array(219, 112, 147),
                __('Pale robin egg blue', 'sadovod-colors') => array(150, 222, 209),
                __('Pale silver', 'sadovod-colors') => array(201, 192, 187),
                __('Pale spring bud', 'sadovod-colors') => array(236, 235, 189),
                __('Pale taupe', 'sadovod-colors') => array(188, 152, 126),
                __('Pale violet red', 'sadovod-colors') => array(219, 112, 147),
                __('Pansy purple', 'sadovod-colors') => array(120, 24, 74),
                __('Papaya whip', 'sadovod-colors') => array(255, 239, 213),
                __('Paris Green', 'sadovod-colors') => array(80, 200, 120),
                __('Pastel blue', 'sadovod-colors') => array(174, 198, 207),
                __('Pastel brown', 'sadovod-colors') => array(131, 105, 83),
                __('Pastel gray', 'sadovod-colors') => array(207, 207, 196),
                __('Pastel green', 'sadovod-colors') => array(119, 221, 119),
                __('Pastel magenta', 'sadovod-colors') => array(244, 154, 194),
                __('Pastel orange', 'sadovod-colors') => array(255, 179, 71),
                __('Pastel pink', 'sadovod-colors') => array(255, 209, 220),
                __('Pastel purple', 'sadovod-colors') => array(179, 158, 181),
                __('Pastel red', 'sadovod-colors') => array(255, 105, 97),
                __('Pastel violet', 'sadovod-colors') => array(203, 153, 201),
                __('Pastel yellow', 'sadovod-colors') => array(253, 253, 150),
                __('Patriarch', 'sadovod-colors') => array(128, 0, 128),
                __('Payne grey', 'sadovod-colors') => array(83, 104, 120),
                __('Peach', 'sadovod-colors') => array(255, 229, 180),
                __('Peach puff', 'sadovod-colors') => array(255, 218, 185),
                __('Peach yellow', 'sadovod-colors') => array(250, 223, 173),
                __('Pear', 'sadovod-colors') => array(209, 226, 49),
                __('Pearl', 'sadovod-colors') => array(234, 224, 200),
                __('Pearl Aqua', 'sadovod-colors') => array(136, 216, 192),
                __('Peridot', 'sadovod-colors') => array(230, 226, 0),
                __('Periwinkle', 'sadovod-colors') => array(204, 204, 255),
                __('Persian blue', 'sadovod-colors') => array(28, 57, 187),
                __('Persian indigo', 'sadovod-colors') => array(50, 18, 122),
                __('Persian orange', 'sadovod-colors') => array(217, 144, 88),
                __('Persian pink', 'sadovod-colors') => array(247, 127, 190),
                __('Persian plum', 'sadovod-colors') => array(112, 28, 28),
                __('Persian red', 'sadovod-colors') => array(204, 51, 51),
                __('Persian rose', 'sadovod-colors') => array(254, 40, 162),
                __('Phlox', 'sadovod-colors') => array(223, 0, 255),
                __('Phthalo blue', 'sadovod-colors') => array(0, 15, 137),
                __('Phthalo green', 'sadovod-colors') => array(18, 53, 36),
                __('Piggy pink', 'sadovod-colors') => array(253, 221, 230),
                __('Pine green', 'sadovod-colors') => array(1, 121, 111),
                __('Pink', 'sadovod-colors') => array(255, 192, 203),
                __('Pink Flamingo', 'sadovod-colors') => array(252, 116, 253),
                __('Pink Sherbet', 'sadovod-colors') => array(247, 143, 167),
                __('Pink pearl', 'sadovod-colors') => array(231, 172, 207),
                __('Pistachio', 'sadovod-colors') => array(147, 197, 114),
                __('Platinum', 'sadovod-colors') => array(229, 228, 226),
                __('Plum', 'sadovod-colors') => array(221, 160, 221),
                __('Portland Orange', 'sadovod-colors') => array(255, 90, 54),
                __('Powder blue', 'sadovod-colors') => array(176, 224, 230),
                __('Princeton orange', 'sadovod-colors') => array(255, 143, 0),
                __('Prussian blue', 'sadovod-colors') => array(0, 49, 83),
                __('Psychedelic purple', 'sadovod-colors') => array(223, 0, 255),
                __('Puce', 'sadovod-colors') => array(204, 136, 153),
                __('Pumpkin', 'sadovod-colors') => array(255, 117, 24),
                __('Purple', 'sadovod-colors') => array(128, 0, 128),
                __('Purple Heart', 'sadovod-colors') => array(105, 53, 156),
                __('Purple Mountain`s Majesty', 'sadovod-colors') => array(157, 129, 186),
                __('Purple mountain majesty', 'sadovod-colors') => array(150, 120, 182),
                __('Purple pizzazz', 'sadovod-colors') => array(254, 78, 218),
                __('Purple taupe', 'sadovod-colors') => array(80, 64, 77),
                __('Rackley', 'sadovod-colors') => array(93, 138, 168),
                __('Radical Red', 'sadovod-colors') => array(255, 53, 94),
                __('Raspberry', 'sadovod-colors') => array(227, 11, 93),
                __('Raspberry glace', 'sadovod-colors') => array(145, 95, 109),
                __('Raspberry pink', 'sadovod-colors') => array(226, 80, 152),
                __('Raspberry rose', 'sadovod-colors') => array(179, 68, 108),
                __('Raw Sienna', 'sadovod-colors') => array(214, 138, 89),
                __('Razzle dazzle rose', 'sadovod-colors') => array(255, 51, 204),
                __('Razzmatazz', 'sadovod-colors') => array(227, 37, 107),
                __('Red', 'sadovod-colors') => array(255, 0, 0),
                __('Red Orange', 'sadovod-colors') => array(255, 83, 73),
                __('Red brown', 'sadovod-colors') => array(165, 42, 42),
                __('Red violet', 'sadovod-colors') => array(199, 21, 133),
                __('Rich black', 'sadovod-colors') => array(0, 64, 64),
                __('Rich carmine', 'sadovod-colors') => array(215, 0, 64),
                __('Rich electric blue', 'sadovod-colors') => array(8, 146, 208),
                __('Rich lilac', 'sadovod-colors') => array(182, 102, 210),
                __('Rich maroon', 'sadovod-colors') => array(176, 48, 96),
                __('Rifle green', 'sadovod-colors') => array(65, 72, 51),
                __('Robin`s Egg Blue', 'sadovod-colors') => array(31, 206, 203),
                __('Rose', 'sadovod-colors') => array(255, 0, 127),
                __('Rose bonbon', 'sadovod-colors') => array(249, 66, 158),
                __('Rose ebony', 'sadovod-colors') => array(103, 72, 70),
                __('Rose gold', 'sadovod-colors') => array(183, 110, 121),
                __('Rose madder', 'sadovod-colors') => array(227, 38, 54),
                __('Rose pink', 'sadovod-colors') => array(255, 102, 204),
                __('Rose quartz', 'sadovod-colors') => array(170, 152, 169),
                __('Rose taupe', 'sadovod-colors') => array(144, 93, 93),
                __('Rose vale', 'sadovod-colors') => array(171, 78, 82),
                __('Rosewood', 'sadovod-colors') => array(101, 0, 11),
                __('Rosso corsa', 'sadovod-colors') => array(212, 0, 0),
                __('Rosy brown', 'sadovod-colors') => array(188, 143, 143),
                __('Royal azure', 'sadovod-colors') => array(0, 56, 168),
                __('Royal blue', 'sadovod-colors') => array(65, 105, 225),
                __('Royal fuchsia', 'sadovod-colors') => array(202, 44, 146),
                __('Royal purple', 'sadovod-colors') => array(120, 81, 169),
                __('Ruby', 'sadovod-colors') => array(224, 17, 95),
                __('Ruddy', 'sadovod-colors') => array(255, 0, 40),
                __('Ruddy brown', 'sadovod-colors') => array(187, 101, 40),
                __('Ruddy pink', 'sadovod-colors') => array(225, 142, 150),
                __('Rufous', 'sadovod-colors') => array(168, 28, 7),
                __('Russet', 'sadovod-colors') => array(128, 70, 27),
                __('Rust', 'sadovod-colors') => array(183, 65, 14),
                __('Sacramento State green', 'sadovod-colors') => array(0, 86, 63),
                __('Saddle brown', 'sadovod-colors') => array(139, 69, 19),
                __('Safety orange', 'sadovod-colors') => array(255, 103, 0),
                __('Saffron', 'sadovod-colors') => array(244, 196, 48),
                __('Saint Patrick Blue', 'sadovod-colors') => array(35, 41, 122),
                __('Salmon', 'sadovod-colors') => array(255, 140, 105),
                __('Salmon pink', 'sadovod-colors') => array(255, 145, 164),
                __('Sand', 'sadovod-colors') => array(194, 178, 128),
                __('Sand dune', 'sadovod-colors') => array(150, 113, 23),
                __('Sandstorm', 'sadovod-colors') => array(236, 213, 64),
                __('Sandy brown', 'sadovod-colors') => array(244, 164, 96),
                __('Sandy taupe', 'sadovod-colors') => array(150, 113, 23),
                __('Sap green', 'sadovod-colors') => array(80, 125, 42),
                __('Sapphire', 'sadovod-colors') => array(15, 82, 186),
                __('Satin sheen gold', 'sadovod-colors') => array(203, 161, 53),
                __('Scarlet', 'sadovod-colors') => array(255, 36, 0),
                __('School bus yellow', 'sadovod-colors') => array(255, 216, 0),
                __('Screamin Green', 'sadovod-colors') => array(118, 255, 122),
                __('Sea blue', 'sadovod-colors') => array(0, 105, 148),
                __('Sea green', 'sadovod-colors') => array(46, 139, 87),
                __('Seal brown', 'sadovod-colors') => array(50, 20, 20),
                __('Seashell', 'sadovod-colors') => array(255, 245, 238),
                __('Selective yellow', 'sadovod-colors') => array(255, 186, 0),
                __('Sepia', 'sadovod-colors') => array(112, 66, 20),
                __('Shadow', 'sadovod-colors') => array(138, 121, 93),
                __('Shamrock', 'sadovod-colors') => array(69, 206, 162),
                __('Shamrock green', 'sadovod-colors') => array(0, 158, 96),
                __('Shocking pink', 'sadovod-colors') => array(252, 15, 192),
                __('Sienna', 'sadovod-colors') => array(136, 45, 23),
                __('Silver', 'sadovod-colors') => array(192, 192, 192),
                __('Sinopia', 'sadovod-colors') => array(203, 65, 11),
                __('Skobeloff', 'sadovod-colors') => array(0, 116, 116),
                __('Sky blue', 'sadovod-colors') => array(135, 206, 235),
                __('Sky magenta', 'sadovod-colors') => array(207, 113, 175),
                __('Slate blue', 'sadovod-colors') => array(106, 90, 205),
                __('Slate gray', 'sadovod-colors') => array(112, 128, 144),
                __('Smalt', 'sadovod-colors') => array(0, 51, 153),
                __('Smokey topaz', 'sadovod-colors') => array(147, 61, 65),
                __('Smoky black', 'sadovod-colors') => array(16, 12, 8),
                __('Snow', 'sadovod-colors') => array(255, 250, 250),
                __('Spiro Disco Ball', 'sadovod-colors') => array(15, 192, 252),
                __('Spring bud', 'sadovod-colors') => array(167, 252, 0),
                __('Spring green', 'sadovod-colors') => array(0, 255, 127),
                __('Steel blue', 'sadovod-colors') => array(70, 130, 180),
                __('Stil de grain yellow', 'sadovod-colors') => array(250, 218, 94),
                __('Stizza', 'sadovod-colors') => array(153, 0, 0),
                __('Stormcloud', 'sadovod-colors') => array(0, 128, 128),
                __('Straw', 'sadovod-colors') => array(228, 217, 111),
                __('Sunglow', 'sadovod-colors') => array(255, 204, 51),
                __('Sunset', 'sadovod-colors') => array(250, 214, 165),
                __('Sunset Orange', 'sadovod-colors') => array(253, 94, 83),
                __('Tan', 'sadovod-colors') => array(210, 180, 140),
                __('Tangelo', 'sadovod-colors') => array(249, 77, 0),
                __('Tangerine', 'sadovod-colors') => array(242, 133, 0),
                __('Tangerine yellow', 'sadovod-colors') => array(255, 204, 0),
                __('Taupe', 'sadovod-colors') => array(72, 60, 50),
                __('Taupe gray', 'sadovod-colors') => array(139, 133, 137),
                __('Tawny', 'sadovod-colors') => array(205, 87, 0),
                __('Tea green', 'sadovod-colors') => array(208, 240, 192),
                __('Tea rose', 'sadovod-colors') => array(244, 194, 194),
                __('Teal', 'sadovod-colors') => array(0, 128, 128),
                __('Teal blue', 'sadovod-colors') => array(54, 117, 136),
                __('Teal green', 'sadovod-colors') => array(0, 109, 91),
                __('Terra cotta', 'sadovod-colors') => array(226, 114, 91),
                __('Thistle', 'sadovod-colors') => array(216, 191, 216),
                __('Thulian pink', 'sadovod-colors') => array(222, 111, 161),
                __('Tickle Me Pink', 'sadovod-colors') => array(252, 137, 172),
                __('Tiffany Blue', 'sadovod-colors') => array(10, 186, 181),
                __('Tiger eye', 'sadovod-colors') => array(224, 141, 60),
                __('Timberwolf', 'sadovod-colors') => array(219, 215, 210),
                __('Titanium yellow', 'sadovod-colors') => array(238, 230, 0),
                __('Tomato', 'sadovod-colors') => array(255, 99, 71),
                __('Toolbox', 'sadovod-colors') => array(116, 108, 192),
                __('Topaz', 'sadovod-colors') => array(255, 200, 124),
                __('Tractor red', 'sadovod-colors') => array(253, 14, 53),
                __('Trolley Grey', 'sadovod-colors') => array(128, 128, 128),
                __('Tropical rain forest', 'sadovod-colors') => array(0, 117, 94),
                __('True Blue', 'sadovod-colors') => array(0, 115, 207),
                __('Tufts Blue', 'sadovod-colors') => array(65, 125, 193),
                __('Tumbleweed', 'sadovod-colors') => array(222, 170, 136),
                __('Turkish rose', 'sadovod-colors') => array(181, 114, 129),
                __('Turquoise', 'sadovod-colors') => array(48, 213, 200),
                __('Turquoise blue', 'sadovod-colors') => array(0, 255, 239),
                __('Turquoise green', 'sadovod-colors') => array(160, 214, 180),
                __('Tuscan red', 'sadovod-colors') => array(102, 66, 77),
                __('Twilight lavender', 'sadovod-colors') => array(138, 73, 107),
                __('Tyrian purple', 'sadovod-colors') => array(102, 2, 60),
                __('UA blue', 'sadovod-colors') => array(0, 51, 170),
                __('UA red', 'sadovod-colors') => array(217, 0, 76),
                __('UCLA Blue', 'sadovod-colors') => array(83, 104, 149),
                __('UCLA Gold', 'sadovod-colors') => array(255, 179, 0),
                __('UFO Green', 'sadovod-colors') => array(60, 208, 112),
                __('UP Forest green', 'sadovod-colors') => array(1, 68, 33),
                __('UP Maroon', 'sadovod-colors') => array(123, 17, 19),
                __('USC Cardinal', 'sadovod-colors') => array(153, 0, 0),
                __('USC Gold', 'sadovod-colors') => array(255, 204, 0),
                __('Ube', 'sadovod-colors') => array(136, 120, 195),
                __('Ultra pink', 'sadovod-colors') => array(255, 111, 255),
                __('Ultramarine', 'sadovod-colors') => array(18, 10, 143),
                __('Ultramarine blue', 'sadovod-colors') => array(65, 102, 245),
                __('Umber', 'sadovod-colors') => array(99, 81, 71),
                __('United Nations blue', 'sadovod-colors') => array(91, 146, 229),
                __('University of California Gold', 'sadovod-colors') => array(183, 135, 39),
                __('Unmellow Yellow', 'sadovod-colors') => array(255, 255, 102),
                __('Upsdell red', 'sadovod-colors') => array(174, 32, 41),
                __('Urobilin', 'sadovod-colors') => array(225, 173, 33),
                __('Utah Crimson', 'sadovod-colors') => array(211, 0, 63),
                __('Vanilla', 'sadovod-colors') => array(243, 229, 171),
                __('Vegas gold', 'sadovod-colors') => array(197, 179, 88),
                __('Venetian red', 'sadovod-colors') => array(200, 8, 21),
                __('Verdigris', 'sadovod-colors') => array(67, 179, 174),
                __('Vermilion', 'sadovod-colors') => array(227, 66, 52),
                __('Veronica', 'sadovod-colors') => array(160, 32, 240),
                __('Violet', 'sadovod-colors') => array(238, 130, 238),
                __('Violet Blue', 'sadovod-colors') => array(50, 74, 178),
                __('Violet Red', 'sadovod-colors') => array(247, 83, 148),
                __('Viridian', 'sadovod-colors') => array(64, 130, 109),
                __('Vivid auburn', 'sadovod-colors') => array(146, 39, 36),
                __('Vivid burgundy', 'sadovod-colors') => array(159, 29, 53),
                __('Vivid cerise', 'sadovod-colors') => array(218, 29, 129),
                __('Vivid tangerine', 'sadovod-colors') => array(255, 160, 137),
                __('Vivid violet', 'sadovod-colors') => array(159, 0, 255),
                __('Warm black', 'sadovod-colors') => array(0, 66, 66),
                __('Waterspout', 'sadovod-colors') => array(0, 255, 255),
                __('Wenge', 'sadovod-colors') => array(100, 84, 82),
                __('Wheat', 'sadovod-colors') => array(245, 222, 179),
                __('White', 'sadovod-colors') => array(255, 255, 255),
                __('White smoke', 'sadovod-colors') => array(245, 245, 245),
                __('Wild Strawberry', 'sadovod-colors') => array(255, 67, 164),
                __('Wild Watermelon', 'sadovod-colors') => array(252, 108, 133),
                __('Wild blue yonder', 'sadovod-colors') => array(162, 173, 208),
                __('Wine', 'sadovod-colors') => array(114, 47, 55),
                __('Wisteria', 'sadovod-colors') => array(201, 160, 220),
                __('Xanadu', 'sadovod-colors') => array(115, 134, 120),
                __('Yale Blue', 'sadovod-colors') => array(15, 77, 146),
                __('Yellow', 'sadovod-colors') => array(255, 255, 0),
                __('Yellow Orange', 'sadovod-colors') => array(255, 174, 66),
                __('Yellow green', 'sadovod-colors') => array(154, 205, 50),
                __('Zaffre', 'sadovod-colors') => array(0, 20, 168),
                __('Zinnwaldite brown', 'sadovod-colors') => array(44, 22, 8)
            );
            #endregion
        }

        $val = get_rgb_from_color($color);

        $distances = array();
        foreach ($color_table as $name => $c) {
            $distances[$name] = get_rgb_distance($c, $val);
            if ($distances[$name] == 0) {
                $color_name = $name;
                break;
            }
        }

        if (empty($color_name)) {
            $minval = current($distances) + 1;
            foreach ($distances as $k => $v) {
                if ($v < $minval) {
                    $minval = $v;
                    $color_name = $k;
                }
            }
        }
    }

    if (!empty($color_name))
        $color_cache[$color] = $color_name;

    return $color_name;
}

function get_rgb_from_color($color)
{
    if ($color[0] == '#')
        $color = substr($color, 1);

    if (strlen($color) == 6)
        list($r, $g, $b) = array(
            $color[0] . $color[1],
            $color[2] . $color[3],
            $color[4] . $color[5]
        );
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array(
            $color[0] . $color[0],
            $color[1] . $color[1], $color[2] . $color[2]
        );
    else
        return false;

    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);

    return array($r, $g, $b);
}

/**
 * @param array $rgb1
 * @param array $rgb2
 * 
 * @return int
 */
function get_rgb_distance(array $rgb1, array $rgb2)
{
    return sqrt(pow($rgb1[0] - $rgb2[0], 2) +
        pow($rgb1[1] - $rgb2[1], 2) +
        pow($rgb1[2] - $rgb2[2], 2));
}

/*  Обработать телефон
-----------------------------------------------*/
function sanitize_phone_number($phone)
{
    return preg_replace('/[^\d+\(\)-]/', '', $phone);
}

/*  Обработать массив
-----------------------------------------------*/
function sanitize_array_field($array)
{
    if (is_string($array)) {
        $array = sanitize_text_field($array);
    } elseif (is_array($array)) {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = sanitize_array_field($value);
            } else {
                $value = sanitize_text_field($value);
            }
        }
    }

    return $array;
}

/*  Города в код
-----------------------------------------------*/
$country_names = array(
    'Абхазия' => 'AB',
    'Австралия' => 'AU',
    'Австрия' => 'AT',
    'Азербайджан' => 'AZ',
    'Албания' => 'AL',
    'Алжир' => 'DZ',
    'Американское Самоа' => 'AS',
    'Ангилья' => 'AI',
    'Ангола' => 'AO',
    'Андорра' => 'AD',
    'Антарктида' => 'AQ',
    'Антигуа и Барбуда' => 'AG',
    'Аргентина' => 'AR',
    'Армения' => 'AM',
    'Аруба' => 'AW',
    'Афганистан' => 'AF',
    'Багамы' => 'BS',
    'Бангладеш' => 'BD',
    'Барбадос' => 'BB',
    'Бахрейн' => 'BH',
    'Беларусь' => 'BY',
    'Белиз' => 'BZ',
    'Бельгия' => 'BE',
    'Бенин' => 'BJ',
    'Бермуды' => 'BM',
    'Болгария' => 'BG',
    'Боливия, Многонациональное Государство' => 'BO',
    'Бонайре, Саба и Синт-Эстатиус' => 'BQ',
    'Босния и Герцеговина' => 'BA',
    'Ботсвана' => 'BW',
    'Бразилия' => 'BR',
    'Британская территория в Индийском океане' => 'IO',
    'Бруней-Даруссалам' => 'BN',
    'Буркина-Фасо' => 'BF',
    'Бурунди' => 'BI',
    'Бутан' => 'BT',
    'Вануату' => 'VU',
    'Венгрия' => 'HU',
    'Венесуэла Боливарианская Республика' => 'VE',
    'Виргинские острова, Британские' => 'VG',
    'Виргинские острова, США' => 'VI',
    'Вьетнам' => 'VN',
    'Габон' => 'GA',
    'Гаити' => 'HT',
    'Гайана' => 'GY',
    'Гамбия' => 'GM',
    'Гана' => 'GH',
    'Гваделупа' => 'GP',
    'Гватемала' => 'GT',
    'Гвинея' => 'GN',
    'Гвинея-Бисау' => 'GW',
    'Германия' => 'DE',
    'Гернси' => 'GG',
    'Гибралтар' => 'GI',
    'Гондурас' => 'HN',
    'Гонконг' => 'HK',
    'Гренада' => 'GD',
    'Гренландия' => 'GL',
    'Греция' => 'GR',
    'Грузия' => 'GE',
    'Гуам' => 'GU',
    'Дания' => 'DK',
    'Джерси' => 'JE',
    'Джибути' => 'DJ',
    'Доминика' => 'DM',
    'Доминиканская Республика' => 'DO',
    'Египет' => 'EG',
    'Замбия' => 'ZM',
    'Западная Сахара' => 'EH',
    'Зимбабве' => 'ZW',
    'Израиль' => 'IL',
    'Индия' => 'IN',
    'Индонезия' => 'ID',
    'Иордания' => 'JO',
    'Ирак' => 'IQ',
    'Иран, Исламская Республика' => 'IR',
    'Ирландия' => 'IE',
    'Исландия' => 'IS',
    'Испания' => 'ES',
    'Италия' => 'IT',
    'Йемен' => 'YE',
    'Кабо-Верде' => 'CV',
    'Казахстан' => 'KZ',
    'Камбоджа' => 'KH',
    'Камерун' => 'CM',
    'Канада' => 'CA',
    'Катар' => 'QA',
    'Кения' => 'KE',
    'Кипр' => 'CY',
    'Киргизия' => 'KG',
    'Кирибати' => 'KI',
    'Китай' => 'CN',
    'Кокосовые острова' => 'CC',
    'Колумбия' => 'CO',
    'Коморы' => 'KM',
    'Конго' => 'CG',
    'Конго, Демократическая Республика' => 'CD',
    'Корея, Народно-Демократическая Республика' => 'KP',
    'Корея, Республика' => 'KR',
    'Коста-Рика' => 'CR',
    'Кот д’Ивуар' => 'CI',
    'Куба' => 'CU',
    'Кувейт' => 'KW',
    'Кюрасао' => 'CW',
    'Лаос' => 'LA',
    'Латвия' => 'LV',
    'Лесото' => 'LS',
    'Ливан' => 'LB',
    'Ливийская Арабская Джамахирия' => 'LY',
    'Либерия' => 'LR',
    'Лихтенштейн' => 'LI',
    'Литва' => 'LT',
    'Люксембург' => 'LU',
    'Маврикий' => 'MU',
    'Мавритания' => 'MR',
    'Мадагаскар' => 'MG',
    'Майотта' => 'YT',
    'Макао' => 'MO',
    'Малави' => 'MW',
    'Малайзия' => 'MY',
    'Мали' => 'ML',
    'Малые Тихоокеанские отдаленные острова Соединенных Штатов' => 'UM',
    'Мальдивы' => 'MV',
    'Мальта' => 'MT',
    'Марокко' => 'MA',
    'Мартиника' => 'MQ',
    'Маршалловы острова' => 'MH',
    'Мексика' => 'MX',
    'Микронезия, Федеративные Штаты' => 'FM',
    'Мозамбик' => 'MZ',
    'Молдова, Республика' => 'MD',
    'Монако' => 'MC',
    'Монголия' => 'MN',
    'Монтсеррат' => 'MS',
    'Мьянма' => 'MM',
    'Намибия' => 'NA',
    'Науру' => 'NR',
    'Непал' => 'NP',
    'Нигер' => 'NE',
    'Нигерия' => 'NG',
    'Нидерланды' => 'NL',
    'Никарагуа' => 'NI',
    'Ниуэ' => 'NU',
    'Новая Зеландия' => 'NZ',
    'Новая Каледония' => 'NC',
    'Норвегия' => 'NO',
    'Объединенные Арабские Эмираты' => 'AE',
    'Оман' => 'OM',
    'Остров Буве' => 'BV',
    'Остров Мэн' => 'IM',
    'Остров Норфолк' => 'NF',
    'Остров Рождества' => 'CX',
    'Остров Херд и острова Макдональд' => 'HM',
    'Острова Кайман' => 'KY',
    'Острова Кука' => 'CK',
    'Острова Теркс и Кайкос' => 'TC',
    'Пакистан' => 'PK',
    'Палау' => 'PW',
    'Палестинская территория, оккупированная' => 'PS',
    'Панама' => 'PA',
    'Папский Престол (Государство — город Ватикан)' => 'VA',
    'Папуа-Новая Гвинея' => 'PG',
    'Парагвай' => 'PY',
    'Перу' => 'PE',
    'Питкерн' => 'PN',
    'Польша' => 'PL',
    'Португалия' => 'PT',
    'Пуэрто-Рико' => 'PR',
    'Республика Македония' => 'MK',
    'Реюньон' => 'RE',
    'Россия' => 'RU',
    'Руанда' => 'RW',
    'Румыния' => 'RO',
    'Самоа' => 'WS',
    'Сан-Марино' => 'SM',
    'Сан-Томе и Принсипи' => 'ST',
    'Саудовская Аравия' => 'SA',
    'Святая Елена, Остров вознесения, Тристан-да-Кунья' => 'SH',
    'Северные Марианские острова' => 'MP',
    'Сен-Бартельми' => 'BL',
    'Сен-Мартен' => 'MF',
    'Сенегал' => 'SN',
    'Сент-Винсент и Гренадины' => 'VC',
    'Сент-Китс и Невис' => 'KN',
    'Сент-Люсия' => 'LC',
    'Сент-Пьер и Микелон' => 'PM',
    'Сербия' => 'RS',
    'Сейшелы' => 'SC',
    'Сингапур' => 'SG',
    'Синт-Мартен' => 'SX',
    'Сирийская Арабская Республика' => 'SY',
    'Словакия' => 'SK',
    'Словения' => 'SI',
    'Соединенное Королевство' => 'GB',
    'Соединенные Штаты' => 'US',
    'Соломоновы острова' => 'SB',
    'Сомали' => 'SO',
    'Судан' => 'SD',
    'Суринам' => 'SR',
    'Сьерра-Леоне' => 'SL',
    'Таджикистан' => 'TJ',
    'Таиланд' => 'TH',
    'Тайвань (Китай)' => 'TW',
    'Танзания, Объединенная Республика' => 'TZ',
    'Тимор-Лесте' => 'TL',
    'Того' => 'TG',
    'Токелау' => 'TK',
    'Тонга' => 'TO',
    'Тринидад и Тобаго' => 'TT',
    'Тувалу' => 'TV',
    'Тунис' => 'TN',
    'Туркмения' => 'TM',
    'Турция' => 'TR',
    'Уганда' => 'UG',
    'Узбекистан' => 'UZ',
    'Украина' => 'UA',
    'Уоллис и Футуна' => 'WF',
    'Уругвай' => 'UY',
    'Фарерские острова' => 'FO',
    'Фиджи' => 'FJ',
    'Филиппины' => 'PH',
    'Финляндия' => 'FI',
    'Фолклендские острова (Мальвинские)' => 'FK',
    'Франция' => 'FR',
    'Французская Гвиана' => 'GF',
    'Французская Полинезия' => 'PF',
    'Французские Южные территории' => 'TF',
    'Хорватия' => 'HR',
    'Центрально-Африканская Республика' => 'CF',
    'Чад' => 'TD',
    'Черногория' => 'ME',
    'Чешская Республика' => 'CZ',
    'Чили' => 'CL',
    'Швейцария' => 'CH',
    'Швеция' => 'SE',
    'Шпицберген и Ян Майен' => 'SJ',
    'Шри-Ланка' => 'LK',
    'Эквадор' => 'EC',
    'Экваториальная Гвинея' => 'GQ',
    'Эландские острова' => 'AX',
    'Эль-Сальвадор' => 'SV',
    'Эритрея' => 'ER',
    'Эсватини' => 'SZ',
    'Эстония' => 'EE',
    'Эфиопия' => 'ET',
    'Южная Африка' => 'ZA',
    'Южная Джорджия и Южные Сандвичевы острова' => 'GS',
    'Южная Осетия' => 'OS',
    'Южный Судан' => 'SS',
    'Ямайка' => 'JM',
    'Япония' => 'JP',
);

function get_country_code($country)
{
    return isset($country_names[$country]) ? $country_names[$country] : '*';
}

/*  Создать инлайн скрипт
-----------------------------------------------*/
function register_inline_script($handle, $dependencies = array(), $script = '', $ver = false, $in_footer = true)
{
    if (!wp_script_is($handle, 'enqueued')) {
        wp_register_script($handle, false, $dependencies, $ver, $in_footer);
        wp_enqueue_script($handle);

        wp_add_inline_script($handle, $script);
    }
}


/*  Создать инлайн стиль
-----------------------------------------------*/
function register_inline_style($handle, $dependencies = array(), $style = '')
{
    if (!wp_style_is($handle, 'enqueued')) {
        wp_register_style($handle, false, $dependencies);
        wp_enqueue_style($handle);

        wp_add_inline_style($handle, $style);
    }
}

function dump_rewrite_rules($where = null, $quit = false)
{
    // only do this on public side
    if (is_admin()) return false;

    // Hook list - https://wp-kama.ru/hooks/actions-order
    switch ($where) {
        case 'request':
            $filter = 'parse_request';
            break;
        default:
            $filter = 'wp_footer';
            break;
    }

    add_action($filter, function () use ($quit) {
        global $wp_rewrite, $wp, $template;

        if (!empty($wp_rewrite->rules)) { ?>

            <div class="content-area rewrite-table">
                <style>
                    h5 {
                        background: #000 !important;
                        color: #fff !important;
                        padding: 1em !important;
                        margin: 1em !important
                    }

                    table {
                        margin: 1em !important
                    }

                    table td {
                        border: 1px solid silver;
                        padding: 5px
                    }

                    tr.matchedrule td {
                        border-color: transparent
                    }

                    tr.matchedrule>td {
                        background: HSLA(28, 100%, 70%, .8)
                    }

                    tr.matchedrule+tr.matchedrule>td {
                        background: HSLA(27.8, 100%, 70%, 0.68)
                    }

                    tr.matchedrule table td+td {
                        font-weight: 700
                    }
                </style>

                <h5>Rewrite Rules</h5>
                <table>
                    <thead>
                        <tr>
                            <td>
                            <td>Rule
                            <td>Rewrite
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($wp_rewrite->rules as $name => $value) {
                            if ($name == $wp->matched_rule) {

                                echo '<tr class="matchedrule">
                    <td>' . $i . '
                    <td>' . $name . '
                    <td>' . $value . '
                    </tr>
                    <tr class="matchedrule">
                    <td colspan="3">
                        <table>
                            <tr><td>Request
                                <td>' . $wp->request . '
                            <tr><td>Matched Rewrite Query
                                <td title="' . urldecode($wp->matched_query) . '">' . $wp->matched_query . '
                            <tr><td>Loaded template
                                <td>' . basename($template) . '
                        </table>
                    </td>
                    </tr>';
                            } else {
                                echo '<tr>
                    <td>' . $i . '
                    <td>' . $name . '
                    <td>' . $value . '
                  </tr>';
                            }
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>

<?php
        }

        if ($quit)
            die();
    });
}

/*  Вывод данных внутри pre
-----------------------------------------------*/
function show_dump($arr, $only_admins = true)
{
    if ($only_admins and (!is_user_logged_in() or (is_user_logged_in() and !current_user_can('manage_options')))) {
        return false;
    }

    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

/*  Вывод пути к функции
-----------------------------------------------*/
function show_backtrace()
{
    $e = new \Exception;
    show_dump($e->getTraceAsString());
}
