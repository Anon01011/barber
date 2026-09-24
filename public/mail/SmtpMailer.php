<?php
/**
 * Standalone Pure Core PHP SMTP Mailer
 * No composer, no external dependencies required.
 */
class SmtpMailer
{
    private $host;
    private $port;
    private $encryption;
    private $username;
    private $password;
    private $timeout = 30;
    private $socket = null;
    private $logs = [];
    private $debug = false;

    public function __construct(array $config)
    {
        $this->host       = $config['smtp_host'] ?? 'localhost';
        $this->port       = (int)($config['smtp_port'] ?? 587);
        $this->encryption = strtolower($config['smtp_encryption'] ?? 'tls');
        $this->username   = $config['smtp_username'] ?? '';
        $this->password   = $config['smtp_password'] ?? '';
        $this->debug      = (bool)($config['debug_mode'] ?? false);
    }

    /**
     * Send an email via SMTP
     *
     * @param string $toEmail
     * @param string $toName
     * @param string $subject
     * @param string $htmlBody
     * @param string $fromEmail
     * @param string $fromName
     * @param string $replyTo
     * @return bool
     * @throws Exception
     */
    public function send($toEmail, $toName, $subject, $htmlBody, $fromEmail, $fromName, $replyTo = '')
    {
        try {
            $this->connect();
            $this->authenticate();

            // MAIL FROM
            $this->sendCommand("MAIL FROM: <{$fromEmail}>", 250);

            // RCPT TO
            $this->sendCommand("RCPT TO: <{$toEmail}>", [250, 251]);

            // DATA
            $this->sendCommand("DATA", 354);

            // Headers & Body Payload
            $boundary = '----=_NextPart_' . md5(uniqid(time()));
            $plainText = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody));

            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
            $headers .= "To: =?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>\r\n";
            if (!empty($replyTo)) {
                $headers .= "Reply-To: <{$replyTo}>\r\n";
            }
            $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $headers .= "X-Mailer: InteliQ Core PHP SMTP Mailer v1.0\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $headers .= "\r\n";

            $message  = $headers;
            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $message .= $plainText . "\r\n\r\n";

            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $message .= $htmlBody . "\r\n\r\n";
            $message .= "--{$boundary}--\r\n";
            $message .= "\r\n.\r\n";

            // Send payload
            $this->write($message);
            $this->readExpected(250);

            // QUIT
            $this->sendCommand("QUIT", 221);
            $this->close();

            return true;
        } catch (Exception $e) {
            $this->close();
            throw $e;
        }
    }

    private function connect()
    {
        $prefix = '';
        if ($this->encryption === 'ssl') {
            $prefix = 'ssl://';
        }

        $remoteSocket = $prefix . $this->host . ':' . $this->port;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ]
        ]);

        $this->log("Connecting to {$remoteSocket}...");
        $this->socket = @stream_socket_client(
            $remoteSocket,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$this->socket) {
            throw new Exception("SMTP Connection Failed: {$errstr} ({$errno})");
        }

        stream_set_timeout($this->socket, $this->timeout);
        $this->readExpected(220);

        // EHLO
        $clientHost = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
        $this->sendCommand("EHLO {$clientHost}", 250);

        // STARTTLS if requested
        if ($this->encryption === 'tls') {
            $this->sendCommand("STARTTLS", 220);
            $crypto = @stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);
            if (!$crypto) {
                // Fallback to generic TLS
                $crypto = @stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                if (!$crypto) {
                    throw new Exception("STARTTLS Crypto Handshake negotiation failed.");
                }
            }
            $this->log("TLS Encryption Established.");
            $this->sendCommand("EHLO {$clientHost}", 250);
        }
    }

    private function authenticate()
    {
        if (empty($this->username) && empty($this->password)) {
            return;
        }

        $this->sendCommand("AUTH LOGIN", 334);
        $this->sendCommand(base64_encode($this->username), 334);
        $this->sendCommand(base64_encode($this->password), 235);
        $this->log("SMTP Authentication Successful.");
    }

    private function sendCommand($command, $expectedCodes)
    {
        $this->log(">> " . (strpos($command, 'AUTH') === false && strlen($command) < 60 ? $command : '[REDACTED_COMMAND]'));
        $this->write($command . "\r\n");
        return $this->readExpected($expectedCodes);
    }

    private function write($data)
    {
        $res = @fwrite($this->socket, $data);
        if ($res === false) {
            throw new Exception("Failed to write to SMTP stream.");
        }
    }

    private function readExpected($expectedCodes)
    {
        if (!is_array($expectedCodes)) {
            $expectedCodes = [$expectedCodes];
        }

        $response = '';
        while (!feof($this->socket)) {
            $line = @fgets($this->socket, 512);
            if ($line === false) {
                break;
            }
            $response .= $line;
            $this->log("<< " . trim($line));

            // RFC 5321: If 4th char is space, it's the final line of the reply
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        $code = (int)substr($response, 0, 3);
        if (!in_array($code, $expectedCodes)) {
            throw new Exception("SMTP Unexpected Response [{$code}]: " . trim($response));
        }

        return $response;
    }

    private function close()
    {
        if ($this->socket) {
            @fclose($this->socket);
            $this->socket = null;
        }
    }

    private function log($msg)
    {
        $this->logs[] = "[" . date('H:i:s') . "] " . $msg;
    }

    public function getLogs()
    {
        return $this->logs;
    }
}
