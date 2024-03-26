<?php

function checkCloudflare($domain) {
    try {
        $response = file_get_contents("http://$domain", false, stream_context_create(["http" => ["timeout" => 10]]));
        $headers = $http_response_header;
        foreach ($headers as $header) {
            if (stripos($header, 'Server:') !== false && stripos($header, 'cloudflare') !== false) {
                return true;
            }
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

function main() {
    $inputFiles = ['iran.txt', 'china.txt'];

    foreach ($inputFiles as $inputFile) {
        $domains = file($inputFile, FILE_IGNORE_NEW_LINES);

        $cloudflareDomains = [];
        $domainsChecked = 1;

        foreach ($domains as $domain) {
            $domain = trim($domain);
            if (checkCloudflare($domain)) {
                echo "\033[92m$domainsChecked $domain => ok\n";
                $cloudflareDomains[] = $domain;
            } else {
                echo "\033[1;31m$domainsChecked $domain => No\n";
            }
            $domainsChecked++;
        }

        file_put_contents($inputFile, "");

        file_put_contents($inputFile, implode("\n", $cloudflareDomains) . PHP_EOL);

        echo "\033[93mCloudflare domains saved to $inputFile\n";
    }
}


main();
?>
