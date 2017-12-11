<?php
/**
 * excel 导出随机密码
 *
 * @author  Sheng <sheng@websay.me>
 * @date    17-12-11
 *
 * @link    http://websay.me
 */

header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");

/**
 * 设置随机数 必须包含小写字母和数字
 * 不包括字母l、o
 * 不包括数字1、0
 * 不能纯数字
 *
 * @param $length
 * @param $number
 * @return array
 */
function setRand($length, $number) {
    $pattern = '123456789abcdefghijklmnpqrstuvwxyz';
    $key = '';
    for($i=0;$i<$number;$i++)
    {
        $key .= $pattern{mt_rand(0,33)};
    }

    $right  = $number;
    $number = $number + 10000; // 容错率1/10000

    for($i = 0; $i < $number; $i++)
    {
        $randNum  = microtime(true) + $i.$key;
        $md5Rand  = md5($randNum);
        $subRand  = substr($md5Rand, 0, $length);
        $str = strtolower($subRand);

        $filterZero  = strpos($str, '0');
        $filterOne   = strpos($str, '1');
        $filterO = strpos($str, 'o');
        $filterL     = strpos($str, 'l');

        $filter = (!is_numeric($str) && (false === $filterZero) && (false === $filterOne) && (false === $filterO) && (false === $filterL));

        if($filter) {
            $n[$i] = $str;
        }

    }

    $unique = array_unique($n);
    $resetIndexUnique = array_values($unique);

    // 去除重复值
    if(count($resetIndexUnique) >= $right) {
        return $resetIndexUnique;
    } else {
        setRand($length, $right);
    }
}

/**
 * 一次性创建 $number 个长度 $length 的随机字符串
 * @param $number
 * @return array
 */
function createRandStr($length, $number) {
    $codes = setRand($length, $number);
    $i = 0;
    while($i < $number){
        $code[$i][] = $codes[$i];
        $i++;
    }
    return $code;
}

/**
 * 导出 excel
 * @param $data
 * @param $excelName
 */
function excel($data, $excelName) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$excelName.xls"); // 表名
    header("Pragma: no-cache"); // 缓存
    header("Expires: 0");

    $sepcol = "\t"; // 分列;
    $sepbr  = "\n"; // 分行

    foreach ($data as $key) {
        foreach ($key as $value) {   // 一行
            echo mb_convert_encoding($value,"GBK","utf-8").$sepcol; // 分列
        }
        echo $sepbr; // 分行
    }
}

// 输出
$excelName = '随机密码生成_'.date('Y-m-d H:i:s');
$length    = 8;
$number    = 20;
$createRandStr = createRandStr($length, $number);
excel($createRandStr, $excelName);
