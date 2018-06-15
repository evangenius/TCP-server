<?php
error_reporting(E_ALL);
/* configuration */
$address = '192.168.43.128';
$port = 10008;

declare (ticks = 1); // how often to check for signals
function sig_handler($signo) {
    global $clients;
    // this function will process sent signals
    if ($signo == SIGTERM || $signo == SIGHUP || $signo == SIGINT) {
        echo "\nPID: " . getmypid() . " I got signal $signo and will exit!\n";
        socket_close_all($clients);
        exit();
    }
}
// These define the signal handling
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP, "sig_handler");
pcntl_signal(SIGINT, "sig_handler");
echo "pid: " . getmypid() . "\n";
echo sprintf("address: %s\nport: %s\n\n", $address, $port);
/* create the server socket and listen for client connections */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $address, $port);
socket_listen($socket);
/* TCP server can accept multiple connections at the same time. */
socket_set_nonblock($socket);
$clients = [];
$fist_access_time = null;
$limitPerSec = 30;
$countInSec = 0;
$drop_count = 0;

/* wait for clients to connect */
while (true) {
    if (($newc = socket_accept($socket)) !== false) {
        /* request rate limit: 30 requests per second */
        if ($fist_access_time === null) {
            $fist_access_time = microtime_float();
        } else {
            if (microtime_float() - $fist_access_time < 1) {
                $countInSec++;
                if ($countInSec > $limitPerSec) {
                    $drop_count++;
                    print_r([
                        // "connections" => $clients,
                        microtime_float() - $fist_access_time,
                        "over 30 requests per second, drop client $newc",
                        "access connection count in one second" => $countInSec,
                        "current connection count" => count($clients),
                    ]);
                    socket_close($newc);
                    continue;
                }
            } else {
                echo __LINE__ . "\n";
                $fist_access_time = null;
                $countInSec = 0;
            }
        }
        // echo "\nclient $newc has connected\n";
        $clients[] = $newc;
        print_r([
            // "connections" => $clients,
            "drop connection count" => $drop_count,
            "current connection count" => count($clients),
        ]);
        $msg = "\nWelcome to the TCP Server. To quit, type 'quit'.\n";
        socket_write($newc, $msg);

        /* TCP server takes in any request text per line and send a query to an external API, until client send 'quit' or timed out. */
        // while (true) {
        //     if (false === ($buf = socket_read($newc, 2048, PHP_NORMAL_READ))) {
        //         echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($newc)) . "\n";
        //         break 2;
        //     }
        //     // echo "buf" . $buf;
        //     if (!$buf = trim($buf)) {
        //         continue;
        //     }
        //     if ($buf == 'quit') {
        //         print_r([$newc, $buf, "quit"]);
        //         if (($key = array_search($newc, $clients)) !== false) {
        //             unset($clients[$key]);
        //         }
        //         break;
        //     }
        //     $talkback = "PHP: You said '$buf'.\n";
        //     socket_write($newc, $talkback, strlen($talkback));
        //     echo "$buf\n";
        // }
        // socket_close($newc);
    }
}
function socket_close_all($clients = []) {
    // echo "\nsocket_close_all\n";
    // print_r($clients);
    foreach ($clients as $val) {
        echo sprintf("socket_close %s\n", $val);
        socket_close($val);
    }
}
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}
?>