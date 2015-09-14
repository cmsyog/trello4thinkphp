<?php
/**
 * Created by PhpStorm.
 * User: guopj
 * Date: 2015/8/24
 * Time: 20:06
 */
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {

    public function index(){

        $this->display('login');
    }
    public function login(){

        $uname = $_POST['username'];
        $pwd = $_POST['password'];

        if(empty($uname)||empty($pwd)){
            $this->error('参数不能为空！');
        }
        $user_model = M('user');
        $where = array(
            'username' => $uname,
        );
       $res = $user_model -> where($where)->find();

        if($res){
            //密码验证
            $check = think_ucenter_decrypt($res['password_hash'],13);
            if($check == $pwd){
                $login_info = array(
                    'uname' => $uname,
                    'id' => $res['id'],
                );
                session('user_info',$login_info);
                cookie('user_info',$login_info);
                $this ->redirect('/admin.php/Index');
            }else{
                $this->error('密码验证错误！','/admin.php/Login');
            }
        }else{
            $this->error('用户不存在！','/admin.php/Login');
        }
    }

    function logout(){

        session('user_info',null);
        cookie('user_info',null);
        $this-> redirect('/admin.php/Login');
    }















//    protected function generateSalt($cost = 13)
//    {
//        $cost = (int) $cost;
//        if ($cost < 4 || $cost > 31) {
//           return 'Cost must be between 4 and 31.';
//        }
//
//        // Get a 20-byte random string
//        $rand = $this->generateRandomKey(20);
//        // Form the prefix that specifies Blowfish (bcrypt) algorithm and cost parameter.
//        $salt = sprintf("$2y$%02d$", $cost);
//        // Append the random salt data in the required base64 format.
//        $salt .= str_replace('+', '.', substr(base64_encode($rand), 0, 22));
//
//        return $salt;
//    }
//
//    /**
//     * Generates specified number of random bytes.
//     * Note that output may not be ASCII.
//     * @see generateRandomString() if you need a string.
//     *
//     * @param integer $length the number of bytes to generate
//     * @return string the generated random bytes
//     * @throws InvalidConfigException if OpenSSL extension is required (e.g. on Windows) but not installed.
//     * @throws Exception on failure.
//     */
//    public function generateRandomKey($length = 32)
//    {
//        /*
//         * Strategy
//         *
//         * The most common platform is Linux, on which /dev/urandom is the best choice. Many other OSs
//         * implement a device called /dev/urandom for Linux compat and it is good too. So if there is
//         * a /dev/urandom then it is our first choice regardless of OS.
//         *
//         * Nearly all other modern Unix-like systems (the BSDs, Unixes and OS X) have a /dev/random
//         * that is a good choice. If we didn't get bytes from /dev/urandom then we try this next but
//         * only if the system is not Linux. Do not try to read /dev/random on Linux.
//         *
//         * Finally, OpenSSL can supply CSPR bytes. It is our last resort. On Windows this reads from
//         * CryptGenRandom, which is the right thing to do. On other systems that don't have a Unix-like
//         * /dev/urandom, it will deliver bytes from its own CSPRNG that is seeded from kernel sources
//         * of randomness. Even though it is fast, we don't generally prefer OpenSSL over /dev/urandom
//         * because an RNG in user space memory is undesirable.
//         *
//         * For background, see http://sockpuppet.org/blog/2014/02/25/safely-generate-random-numbers/
//         */
//
//        $bytes = '';
//
//        // If we are on Linux or any OS that mimics the Linux /dev/urandom device, e.g. FreeBSD or OS X,
//        // then read from /dev/urandom.
//        if (@file_exists('/dev/urandom')) {
//            $handle = fopen('/dev/urandom', 'r');
//            if ($handle !== false) {
//                $bytes .= fread($handle, $length);
//                fclose($handle);
//            }
//        }
//
//        if (StringHelper::byteLength($bytes) >= $length) {
//            return StringHelper::byteSubstr($bytes, 0, $length);
//        }
//
//        // If we are not on Linux and there is a /dev/random device then we have a BSD or Unix device
//        // that won't block. It's not safe to read from /dev/random on Linux.
//        if (PHP_OS !== 'Linux' && @file_exists('/dev/random')) {
//            $handle = fopen('/dev/random', 'r');
//            if ($handle !== false) {
//                $bytes .= fread($handle, $length);
//                fclose($handle);
//            }
//        }
//
//        if (StringHelper::byteLength($bytes) >= $length) {
//            return StringHelper::byteSubstr($bytes, 0, $length);
//        }
//
//        if (!extension_loaded('openssl')) {
//            throw new InvalidConfigException('The OpenSSL PHP extension is not installed.');
//        }
//
//        $bytes .= openssl_random_pseudo_bytes($length, $cryptoStrong);
//
//        if (StringHelper::byteLength($bytes) < $length || !$cryptoStrong) {
//            throw new Exception('Unable to generate random bytes.');
//        }
//
//        return StringHelper::byteSubstr($bytes, 0, $length);
//    }
}