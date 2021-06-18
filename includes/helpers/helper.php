<?php
/**
 * Generate HTML Pagination
 *
 * @param string $url
 * @param integer $current
 * @param integer $perpage
 * @param integer $total
 * @param string $classitem
 * @param string $active
 * @param boolean $extra
 * @return string html
 */
function lsdc_pagination(string $url, int $current = 1, int $perpage = 5, int $total = 6, string $classitem = 'page-item', string $active = 'active', bool $extra = false)
{
    $part = ceil($total / $perpage);
    $middle = ceil($part / 2);
    $current = min(max(1, $current), $total);
    $next = true;
    $prev = true;

    $pagination = '<ul class="pagination">';
    if ($part == $current) {
        $next = false;
    }
    // Disale Next on Last == Current
    if (1 == $current) {
        $prev = false;
    }
    // Disale Next on Last == Current

    if ($prev) {
        $pagination .= '<li class="' . $classitem . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . ($current - 1) . $extra . '">' . __('Prev', 'lsdc') . '</a></li>';
    }

    if ($part == 1) { // Minium Item
        $pagination .= '<li class="' . $classitem . ' ' . $active . ' active"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=1' . $extra . '">' . $current . '</a></li>';
    } elseif ($part < 4) { // 3 Pages
        for ($i = 1; $i <= $part; $i++) {
            if ($i == $current) {
                $pagination .= '<li class="' . $classitem . ' ' . $active . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . $i . $extra . '">' . $i . '</a></li>';
            } else {
                $pagination .= '<li class="' . $classitem . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . $i . $extra . '">' . $i . '</a></li>';
            }
        }
    } else { // Many Pages

        // Starter Page
        for ($i = 1; $i <= $part; $i++) {
            if ($i < 3) { // FIrst 2 Pages
                if ($i == $current) {
                    $pagination .= '<li class="' . $classitem . ' ' . $active . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . $i . $extra . '">' . $i . '</a></li>';
                } else {
                    $pagination .= '<li class="' . $classitem . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . $i . $extra . '">' . $i . '</a></li>';
                }
            }
        }

        $tmp = array();
        if ($current > 2 && $current < $total - 2) { // Current More than 2, and Current Not 8 in 10
            if ($current != 3 && $current < $total - 2) {
                $pagination .= '<li class="' . $classitem . '"><span>...</span></li>';
            }

            for ($i = $current; $i < $part; $i++) {
                if ($i > 2 && $i < $part - 1 && count($tmp) < 2) {
                    if ($i == $current) {
                        $pagination .= '<li class="' . $classitem . ' ' . $active . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . $i . $extra . '">' . $i . '</a></li>';
                    } else {
                        $pagination .= '<li class="' . $classitem . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . $i . $extra . '">' . $i . '</a></li>';
                    }
                    array_push($tmp, $i);
                }
            }

            if ($current < $part - 2) { // If Lower Than Part - 9 Disable
                $pagination .= '<li class="' . $classitem . '"><span>...</span></li>';
            }
        } else {
            $pagination .= '<li class="' . $classitem . '"><span>...</span></li>';
        }

        for ($i = 1; $i <= $part; $i++) {

            if ($i > $part - 2) {
                if ($i == $current) {
                    $pagination .= '<li class="' . $classitem . ' ' . $active . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . $i . $extra . '">' . $i . '</a></li>';
                } else {
                    $pagination .= '<li class="' . $classitem . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . $i . $extra . '">' . $i . '</a></li>';
                }
            }
        }
    }

    if ($next) {
        $pagination .= '<li class="' . $classitem . '"><a href="' . $url . (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'step=' . ($current + 1) . $extra . '">' . __('Next', 'lsdc') . '</a></li>';
    }

    $pagination .= '<ul>';
    echo $pagination;
}

/**
 * Uploader Image to Media
 * using path, filenamme and post id
 *
 * @param string $path
 * @param string $filename
 * @param integer $post_id
 * @return void
 */
function lsdc_image_upload(string $path, string $filename, int $post_id)
{
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($path);

    if (wp_mkdir_p($upload_dir['path'])) {
        $file = $upload_dir['path'] . '/' . $filename;
    } else {
        $file = $upload_dir['basedir'] . '/' . $filename;
    }

    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit',
    );
    $attach_id = wp_insert_attachment($attachment, $file, $post_id);
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);

    set_post_thumbnail($post_id, $attach_id);
}

/**
 * Substring the Word ...
 *
 * @param string $text
 * @param integer $length
 * @return string with ...
 */
function lsdc_substr_word(string $text, int $length)
{
    if (strlen($text) < $length) {
        return $text;
    }

    $text = strip_tags($text);
    $text = substr($text, 0, $length);
    $rpos = strrpos($text, ' ');
    if ($rpos > 0) {
        $text = substr($text, 0, $rpos) . '...';
    }

    return $text;
}

/**
 * CleanUp Html
 *
 * @param string $html
 * @return string html
 */
function lsdc_html_clean(string $html)
{
    $clean = preg_replace('/<!--(.|\s)*?-->/', '', $html);
    $clean = preg_replace('/\s+/', ' ', $clean);
    return trim(preg_replace('/>\s</', '><', $clean));
}

function lsdc_check_reports()
{
    global $wpdb;
    if (in_array('lsdconation_reports', $wpdb->tables)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Sanitize ID
 *
 * @param string $id
 * @return string LSD Plugins -> lsd_plugins
 */
function lsdc_sanitize_id($id)
{
    $id = str_replace(" ", "_", $id);
    $id = preg_replace("/[^a-z_]+/i", "", $id);
    return sanitize_title(strtolower($id));
}

/**
 * Generate Log
 *
 * @param string $name
 * @param string $path
 * @param string $message
 * @return void
 */
function lsdc_generate_log($name, $path, $message)
{
    if (is_array($message)) {
        $message = json_encode($message);
    }

    file_put_contents($path . '/log-' . $name . '.txt', date('Y-m-d H:i:s', current_time('timestamp', 0)) . " :: " . $message . PHP_EOL, FILE_APPEND);
}

/**
 * Increases or decreases the brightness of a color by a percentage of the current brightness.
 * Source : https://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
 */
function lsdc_adjust_brightness($hex, $steps)
{
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color = hexdec($color); // Convert to decimal
        $color = max(0, min(255, $color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code

    }

    return $return;
}

/**
 * Source : https://datayze.com/howto/minify-css-with-php
 */
function lsdc_minify_css($css)
{
    $css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
    $css = preg_replace('/\s{2,}/', ' ', $css);
    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
    $css = preg_replace('/;}/', '}', $css);
    return $css;
}

/**
 * Formatting User Name ( Nama Depan Nama Belakang  = namadepannamabelakang )
 * Block : Format | User
 * @param string $fullname
 */
function lsdc_format_username($fullname)
{
    $names = explode(' ', $fullname);
    $names = array_map('esc_attr', $names); // Sanitize Array
    return strtolower(implode('', $names));
}

/**
 * Formatting Indonesian Phone Number
 * Block : Format
 * @param string $fullname
 */
function lsdc_format_phone($phone)
{
    $phone = (string) $phone;

    if ( strpos($phone, '+62') !== false )
    {
        $format = str_replace( '+62', '0' );
    }else if ( isset( $phone[0] ) && $phone[0] != '0')
    {
        if ( isset( $phone[1] ) && $phone[1] != '6')
        {
            $format = '0' . $phone;
        }
        else
        {
            $format = $phone;
        }
    }
    else
    {
        $format = $phone;
    }

    // Checking Phone Length
    if (strlen($phone) > 13 || strlen($phone) < 11)
    {
        $format = null;
    }

    return trim($format);
}