<?php
/**
 * ci函数库
 * 亓雪峰
 */

if (function_exists('ci')) {
    /**
     * 格式化输出函数
     */
    function ci()
    {
        $CI = &get_instance();
        return $CI;
    }
}

if (!function_exists('post')) {
    /**
     * 获取post的数据
     * 亓雪峰
     */
    function post($index = null, $xss_clean = false, $default = null)
    {
        $CI   = ci();
        $data = $CI->input->post($index, $xss_clean);
        if (!$data && $default) {
            $data = $default;
        }
        return $data;
    }
}
if (!function_exists('get')) {

    /**
     * 获取get的数据
     * 亓雪峰
     */
    function get($index = null, $xss_clean = false, $default = null)
    {
        $CI   = ci();
        $data = $CI->input->get($index, $xss_clean);
        if (!$data && $default) {
            $data = $default;
        }
        return $data;
    }
}

/**
 * 获取配置信息
 */
function config($key, $default = '', $file = '')
{
    $CI = ci();

    if ($file) {
        $CI->config->load($file);
    }

    $item = $CI->config->item($key);

    if ($item) {
        return $item;
    } elseif (!$item && $default) {
        return $default;
    } elseif (!$item && !$default) {
        return false;
    }
}
