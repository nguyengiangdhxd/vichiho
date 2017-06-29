<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Util extends Model
{
    public static function getWorkingMonthSequence(){
        $year = date("Y");
        $month = date('n');
        $count_year = intval($year) - 2014;
        $count_month = $count_year*12 + $month;
        return $count_month;
    }

    public static function formatNumber($number){
//        $whole = floor($number);
        $decimal = fmod($number, 1);
        if($decimal == 0){
            return number_format($number, 0, ",", ".");
        }else{
            return number_format($number, 2, ",", ".");
        }
    }

    public static function cleanVietnamese($str)
    {
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ|ä|å|æ',
            'd'=>'đ|ð',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị|î|ï',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ|Ä|Å|Æ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ|Ë',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị|Î|Ï',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($unicode as $nonUnicode=>$uni)
        {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }

    public static function formatDate($date){
        if(empty($date)){
            return '';
        }

        if(date('Y', strtotime($date)) == date('Y')){
            return date('H:i d/m', strtotime($date));
        }else{
            return date('H:i d/m/Y', strtotime($date));
        }
    }

    /**
     * generate a random token string
     *
     * @param $length
     * @param bool $alphabet
     * @param bool $uppercase
     * @return string
     */
    public static function getToken($length, $alphabet = true, $uppercase = true){
        $token = '';
        $codeAlphabet = '';
        if ($alphabet) {
            $codeAlphabet = "abcdefghijklmnopqrstuvwxyz";

            if ($uppercase) {
                $codeAlphabet .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            }
        }
        $codeAlphabet .= "0123456789";
        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[self::cryptoRandSecure(0,strlen($codeAlphabet))];
        }
        return $token;
    }

    /**
     * Get random lucky number
     *
     * @param $length
     * @return string
     */
    public static function getLuckyNumber($length) {
        $token = '';
        $codeAlphabet = "356789";
        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[self::cryptoRandSecure(0,strlen($codeAlphabet))];
        }
        return $token;
    }

    public static function cryptoRandSecure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    /**
     * Generate random string using SHA1 cryptography
     * @param $length
     * @return string
     */
    public static function randSha1($length) {
        $max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; ++$i) {
            $random .= sha1(uniqid() . mt_rand());
        }
        return substr($random, 0, $length);
    }

    /**
     * Generate random string using MD5 cryptography
     * @param $length
     * @return string
     */
    public static function randMd5($length) {
        $max = ceil($length / 32);
        $random = '';
        for ($i = 0; $i < $max; ++$i) {
            $random .= md5(uniqid() .mt_rand());
        }
        return substr($random, 0, $length);
    }

    /**
     * Check empty array, null value, empty string
     * @param $value
     * @param bool $trim
     * @return bool
     */
    public static function isEmpty($value, $trim = false) {
        return $value===null || $value===array() || $value==='' || $trim && is_scalar($value) && trim($value)==='';
    }

    /**
     * Check valid email format
     * @param $email
     * @return int
     */
    public static function isValidEmail($email){
        return preg_match('/^([a-z0-9]+([_\.\-]{1}[a-z0-9]+)*){1}([@]){1}([a-z0-9]+([_\-]{1}[a-z0-9]+)*)+(([\.]{1}[a-z]{2,6}){0,3}){1}$/i', $email);
    }

    /**
     * Check valid price format
     *
     * @param $value
     * @return int
     */
    public static function isValidPriceFormat($value) {
        return preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $value);
    }

    /**
     * Check valid username format
     * username allow a-zA-Z0-9, "_" and "-", length from 3 to 16 characters
     * @param $name
     * @return int
     */
    public static function isValidUsername($name){
        return preg_match("/^[A-Za-z0-9_]{3,16}$/",$name);
    }

    /**
     * Check is valid password
     * @param $password
     * @return int
     */
    public static function isValidPassword($password){
        return preg_match("/^[a-z0-9_-]{6,18}$/",$password);
    }

    /**
     * Check is valid url
     *
     * @param $url
     * @return int
     */
    public static function isValidUrl($url){
        return preg_match("/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/",$url);
    }

    /**
     * Check valid ip address
     * @param $ip
     * @return int
     */
    public static function isValidIPAddress($ip){
        return preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/",$ip);
    }

    /**
     * Check valid html tags
     * @param $tag
     * @return int
     */
    public static function isValidHtmlTag($tag){
        return preg_match("/^<([a-z]+)([^<]+)*(?:>(.*)<\/\1>|\s+\/>)$/",$tag);
    }

    /**
     * Check valid hex value
     * @param $value
     * @return int
     */
    public static function isValidHexValue($value){
        return preg_match("/^#?([a-f0-9]{6}|[a-f0-9]{3})$/",$value);
    }

    /**
     * Check valid phone number
     * @param $phone
     * @return int
     */
    public static function isValidPhoneNumber($phone) {
        return preg_match("/^([0-9\(\)\/\+ \-]*)$/", $phone);
    }

    /**
     * Validate date with format
     * @param $date
     * @param string $format
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
