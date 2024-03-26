<?php

function check_port($domain, $port) {
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($sock, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 2, 'usec' => 0)); // Timeout for connection attempt
    $result = socket_connect($sock, $domain, $port);
    socket_close($sock);
    if ($result === false) {
        return false;
    } else {
        return true;
    }
}

function main() {
    $domains_file = "iran.txt";
    $ports = array(2052,2086,2082,443,2053,2087,2083); // Example list of ports to check
    $output_files = array();
    foreach ($ports as $port) {
        $output_files[$port] = "result_" . $port . ".txt";
    }
    $common_ports_file = "final_result.txt";
    $common_ports = array();

    $domains = file($domains_file, FILE_IGNORE_NEW_LINES);

    foreach ($domains as $domain) {
        foreach ($ports as $port) {
            if (check_port($domain, $port)) {
                $output_file = fopen($output_files[$port], "a");
                fwrite($output_file, $domain . ":" . $port . " - OPEN\n");
                fclose($output_file);
                if (!in_array($domain, $common_ports)) {
                    array_push($common_ports, $domain);
                }
            }
        }
    }

    $common_ports_file_handle = fopen($common_ports_file, "w");
    foreach ($common_ports as $domain) {
        fwrite($common_ports_file_handle, $domain . "\n");
    }
    fclose($common_ports_file_handle);
}

main();

?>
