<?php
/**
 * 函数库文件
 * 亓雪峰
 */

if (!function_exists('p')) {
    /**
     * 格式化输出数据
     */
    function p($arr)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
}
if (!function_exists('array_column')) {
    /**
     * 返回数组中的一列
     * @param  array  $array      输入数组
     * @param  string $column_key 获取的列
     * @param  string $index_key  指定键值
     * @return array              单列数组
     * @author 亓雪峰
     */
    function array_column(array $array, $column_key, $index_key = null)
    {
        $output = array();
        if (null === $index_key) {
            foreach ($input as $v) {
                $output[] = $v[$column_key];
            }
        } else {
            foreach ($input as $v) {
                $outputKey          = $v[$index_key];
                $output[$outputKey] = $v[$column_key];
            }
        }
        return $output;
    }
}

if (!function_exists('ip')) {
    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    function ip($type = 0, $adv = false)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if ($ip !== null) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }

                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}

/**
 * 二维数组按照指定字段(例如时间)排序
 *
 * 实例:
 * $array[0] = array('key_a' => 'z', 'key_b' => 'c');
 * $array[1] = array('key_a' => 'x', 'key_b' => 'b');
 * $array[2] = array('key_a' => 'y', 'key_b' => 'a');
 *
 * usort($array, sorter('key_b'));
 */
function sorter($key)
{
    return function ($a, $b) use ($key) {
        return strnatcmp($a[$key], $b[$key]);
    };
}

/*-------------------------------- 文件系统 --------------------------------*/

if (!function_exists('mkdirs')) {
    /**
     * 递归创建目录
     * 亓雪峰
     */
    function mkdirs($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdirs(dirname($dir))) {
                return false;
            }
            if (!mkdir($dir, 0777)) {
                return false;
            }
        }
        return true;
    }
}
/**
 * 远程文件的大小
 */
function remoteFileSize($url)
{
    $url = parse_url($url);
    if ($fp = @fsockopen($url['host'], empty($url['port']) ? 80 : $url['port'], $error)) {
        fputs($fp, "GET " . (empty($url['path']) ? '/' : $url['path']) . " HTTP/1.1\r\n");
        fputs($fp, "Host:$url[host]\r\n\r\n");
        while (!feof($fp)) {
            $tmp = fgets($fp);
            if (trim($tmp) == '') {
                break;
            } else if (preg_match('/Content-Length:(.*)/si', $tmp, $arr)) {
                return trim($arr[1]);
            }
        }
        return null;
    } else {
        return null;
    }
}

/**
 * 检测远程文件是否存在
 */
function remoteFileExists($url)
{
    $curl = curl_init($url);
    // 不取回数据
    curl_setopt($curl, CURLOPT_NOBODY, true);
    // 发送请求
    $result = curl_exec($curl);
    $found  = false;
    // 如果请求没有发送失败
    if ($result !== false) {
        // 再检查http响应码是否为200
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            $found = true;
        }
    }
    curl_close($curl);
    return $found;
}

/**
 * 跳转地址
 * redirectUrl('http://www.baidu.com/link?url=1rr24-9xHCvERVJUiMauKDjhVVhgHNeatFAPAxyn9Fkpo2gUVmDfWHRJMUhkF4l3');
 */
function redirectUrl($url)
{
    $url = str_replace(' ', '', $url);
    do {
        //do.while循环：先执行一次，判断后再是否循环
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $header = curl_exec($curl);
        curl_close($curl);
        preg_match('|Location:\s(.*?)\s|', $header, $tdl);
        if (strpos($header, "Location:")) {
            $url = $tdl ? $tdl[1] : null;
        } else {
            return $url . '';
            break;
        }
    } while (strpos($header, "Location:"));
}

/*-------------------------------- 字符串 --------------------------------*/
if (!function_exists('matchEmail')) {
    /**
     * Validate email address
     *
     * @deprecated  3.0.0   Use PHP's filter_var() instead
     * @param   string  $email
     * @return  bool
     *
     * 来自于CodeIgniter3.x
     */
    function matchEmail($email)
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
        // return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE; // 这是CodeIgniter2.x中的代码
    }
}

if (!function_exists('matchMobile')) {
    /**
     * 正则表达式：验证手机号
     */
    function matchMobile($mobile)
    {
        return preg_match("/^13[0-9]{9}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", $mobile);
    }
}

if (!function_exists('likeString')) {
    /**
     * 返回 用户SQL语句中模糊搜索的字符串
     * 致青春 => %致%青%春%
     */
    function likeString($keyword)
    {
        preg_match_all("|.|u", $keyword, $arr);
        $string = '%' . implode("%", $arr[0]) . '%';
        return $string;
    }
}

if (!function_exists('matchPassword')) {
    /**
     * 验证密码
     * @param  string $password
     * @return bool
     *
     * 规则:
     * 1.密码长度至少6位
     *
     * TODO
     * 1.只能由字母, 数字和特殊符号组成
     * 2.字母, 数字和特殊符号至少含有两种
     */
    function matchPassword($password)
    {
        return (bool) preg_match('/^.{6,}$/', $password);
    }
}

// HTTP 函数

if (!function_exists('context')) {
    /**
     * 模拟头部信息,生成context
     * TODO 后期扩展为随机切换代理
     */
    function context()
    {
        //模拟头部信息
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36\r\n",
            ),

        );
        $context = stream_context_create($opts);
        return $context;
    }

}

/*-------------------------------- 时间函数 --------------------------------*/

/**
 * 获取程序的执行时间
 * @author 亓雪峰
 * @param $start_time 程序开始执行的microtime时间
 *
 * 用法实例:
 * $start_time = microtime(true); //获取程序开始执行的时间
 * for ($i = 1; $i <= 10000; $i++) {}//为了实现有一定的时间差,所以用了一个FOR来消耗一些资源.
 * echo getTime($start_time);
 *
 */
function getTime($start_time)
{

    $end_time  = microtime(true); //获取程序执行结束的时间
    $time      = $end_time - $start_time; //计算差值
    $str_total = var_export($time, true);
    if (substr_count($str_total, "E")) {
        //为了避免1.28746032715E-005这种结果的出现,做了一下处理.
        $float_total = floatval(substr($str_total, 5));
        $time        = $float_total / 100000;
    }
    return $time . '秒';
}
