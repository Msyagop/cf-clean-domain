<?php

function checkCloudflare($domain) {
    try {
        $response = file_get_contents("http://$domain", false, stream_context_create(["http" => ["timeout" => 5]]));
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
    $inputFile = 'domains.txt';
    $count = 600;

    $domains = file($inputFile, FILE_IGNORE_NEW_LINES);

    $cloudflareDomains = [];
    $domainsChecked = 1;

    foreach ($domains as $domain) {
        if ($domainsChecked == $count) {
            break;
        }
        $domain = trim($domain);
        if (checkCloudflare($domain)) {
            echo "\033[92m$domainsChecked $domain => ok\n";
            $cloudflareDomains[] = $domain;
        } else {
            echo "\033[1;31m$domainsChecked $domain => No\n";
        }
        $domainsChecked++;
    }

    file_put_contents("iran.txt", implode("\n", $cloudflareDomains) . PHP_EOL, FILE_APPEND);
    echo "\033[93mCloudflare domains saved to iran.txt\n";

    // Remove checked domains from domains.txt
    $remainingDomains = array_slice($domains, $domainsChecked);
    file_put_contents($inputFile, implode("\n", $remainingDomains) . PHP_EOL);

    echo "Checked domains removed from domains.txt\n";
}

main();
?>
