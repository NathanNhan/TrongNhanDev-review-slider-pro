<?php 

/** 
 * Sử dụng: 
 * CLI: php generate-license.php 
*/ 
class SecuredLicenseGenerator {
    //Get Secret Key
    private function get_secret() {
        $secret_file = __DIR__ . '/.secret.php';
        if(file_exists($secret_file)) {
            $secret = include $secret_file;
            if(is_array($secret)) {
                return isset($secret['secret']) ? $secret['secret'] : [];
            }
            if(!empty($secret)) {
                return $secret;
            } 
        } 
    }
    //Kiểm tra file secret key có tồn tại hay không.
    public function has_secret_file () {
        return file_exists(__DIR__ . "/.secret.php");
    }

    // Tạo license key theo số lượng người dùng nhập vào
    public function generate($count = 1) {
        //output: xxxx-xxxx-xxxx-xxxx
        $licenses = [];
        $secret = $this->get_secret();
        for($i = 0; $i < $count; $i++) {
            $part1 = $this->random_string(4);
            $part2 = $this->random_string(4);
            $part3 = $this->random_string(4);

            $checksum = strtoupper(substr(md5($part1 . $part2 . $part3 . $secret), 0, 4));

            $licenses[] = "$part1-$part2-$part3-$checksum";
        }

        return $licenses;

    }

    //hàm random string
    private function random_string($lenght = 4) {
       $chars ='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
       $result = "";
       for($i = 0; $i < $lenght; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
       }
       return $result;
    }

    //verify license
    public function verify($license_key) {
        if (!preg_match('/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $license_key)) {
            return false;
        }
        
        $parts = explode('-', $license_key);
        $secret = $this->get_secret();
        $expected = strtoupper(substr(md5($parts[0] . $parts[1] . $parts[2] . $secret), 0, 4));
        return $parts[3] === $expected;

    }
}

//CLI MODE
if(php_sapi_name() === 'cli') {
    $generator = new SecuredLicenseGenerator();

    echo "---------------- \n";
    echo "|| Trong Nhan Dev License Key generator \n";
    echo "-------------------- \n\n";
    //Check secret file
    if(!$generator->has_secret_file()) {
        echo "Chưa có file .secret.php \n";
        echo "Đang dùng secret mặc định (không khuyến khích) \n\n";
    } else {
        echo "Secret file: .secret.php\n\n";
    }

    echo "Số lượng license cần tạo (default =1): ";
    $count = trim(fgets(STDIN));
    $count = empty($count) ? 1 : (int) $count; 

    if($count > 100) {
        echo "Giới hạn tối đa: 100 key/lần \n";
        $count = 100;
    }

    echo "\n Đang tạo license keys... \n\n";

    $licenses = $generator->generate($count);

    echo "----------------------------- \n";
    echo "LICENSE KEYS \n";
    echo "------------------------------------ \n\n";

    foreach($licenses as $i => $key) {
        $verify = $generator->verify($key) ? "Valid" : "Invalid";
        
        printf("%3d. %s %s\n", $i + 1, $key, $verify);
    }

    echo "\n" . str_repeat("-",60) . "\n";
    echo "Tổng: " . count($licenses) . "keys \n";
    echo "\n" . str_repeat("-",60) . "\n\n";

    echo "Lưu vào file? (y/n):";
    $save = trim(fgets(STDIN));

    if(strtolower($save) === 'y') {
        $filename = 'licenses_' . date('Y-m-d_His') . '.txt';
        file_put_contents($filename, implode("\n", $licenses));
        echo "Đã lưu vào: $filename\n";
    }
    echo "\n Hoàn thành!\n\n";   
}


