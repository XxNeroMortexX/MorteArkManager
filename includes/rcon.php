<?php
/**
 * Source RCON Protocol Implementation
 * For ARK: Survival Evolved Server Management
 */

class RCON {
    private $socket;
    private $authorized = false;
    private $password;
    private $host;
    private $port;
    private $timeout;
    private $requestId = 0;

    const SERVERDATA_AUTH = 3;
    const SERVERDATA_AUTH_RESPONSE = 2;
    const SERVERDATA_EXECCOMMAND = 2;
    const SERVERDATA_RESPONSE_VALUE = 0;

    public function __construct($host, $port, $password, $timeout = 3) {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->timeout = $timeout;
    }

    /**
     * Connect to the RCON server
     */
    public function connect() {
        $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        
        if (!$this->socket) {
            throw new Exception("Could not connect to RCON: $errstr ($errno)");
        }

        stream_set_timeout($this->socket, $this->timeout);
        stream_set_blocking($this->socket, true);

        return $this->authorize();
    }

    /**
     * Authorize with the RCON server
     */
    private function authorize() {
        $this->writePacket(self::SERVERDATA_AUTH, $this->password);
        $response = $this->readPacket();

        if ($response === false) {
            throw new Exception("Authorization failed: No response from server");
        }

        if ($response['id'] == -1) {
            throw new Exception("Authorization failed: Invalid password");
        }

        $this->authorized = true;
        return true;
    }

    /**
     * Execute a command on the server
     */
    public function command($command) {
        if (!$this->authorized) {
            throw new Exception("Not authorized. Call connect() first.");
        }

        $this->writePacket(self::SERVERDATA_EXECCOMMAND, $command);
        $response = $this->readPacket();

        if ($response === false) {
            return false;
        }

        return $response['body'];
    }

    /**
     * Write a packet to the server
     */
    private function writePacket($type, $body) {
        $id = ++$this->requestId;
        
        $data = pack('VV', $id, $type);
        $data .= $body . "\x00";
        $data .= "\x00";

        $packet = pack('V', strlen($data)) . $data;

        fwrite($this->socket, $packet, strlen($packet));
    }

    /**
     * Read a packet from the server
     */
    private function readPacket() {
        $sizeData = fread($this->socket, 4);
        
        if (strlen($sizeData) < 4) {
            return false;
        }

        $size = unpack('V', $sizeData)[1];

        if ($size < 10) {
            return false;
        }

        $packet = '';
        $bytesRead = 0;

        while ($bytesRead < $size) {
            $chunk = fread($this->socket, $size - $bytesRead);
            if ($chunk === false || strlen($chunk) === 0) {
                break;
            }
            $packet .= $chunk;
            $bytesRead += strlen($chunk);
        }

        if ($bytesRead < $size) {
            return false;
        }

        $data = unpack('Vid/Vtype', substr($packet, 0, 8));
        $body = substr($packet, 8, -2);

        return [
            'id' => $data['id'],
            'type' => $data['type'],
            'body' => $body
        ];
    }

    /**
     * Disconnect from the server
     */
    public function disconnect() {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
            $this->authorized = false;
        }
    }

    /**
     * Destructor - ensure socket is closed
     */
    public function __destruct() {
        $this->disconnect();
    }
}

/**
 * Helper function to execute RCON command
 */
function executeRCON($host, $port, $password, $command) {
    try {
        $rcon = new RCON($host, $port, $password);
        $rcon->connect();
        $response = $rcon->command($command);
        $rcon->disconnect();
        
        return [
            'success' => true,
            'response' => $response
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Common ARK RCON Commands
 */
class ARKCommands {
    public static function saveWorld() {
        return 'SaveWorld';
    }

    public static function broadcast($message) {
        return 'Broadcast ' . escapeshellarg($message);
    }

    public static function listPlayers() {
        return 'ListPlayers';
    }

    public static function kickPlayer($steamId) {
        return 'KickPlayer ' . $steamId;
    }

    public static function banPlayer($steamId) {
        return 'BanPlayer ' . $steamId;
    }

    public static function unbanPlayer($steamId) {
        return 'UnbanPlayer ' . $steamId;
    }

    public static function destroyWildDinos() {
        return 'DestroyWildDinos';
    }

    public static function doExit() {
        return 'DoExit';
    }

    public static function setTimeOfDay($hour, $minute = 0) {
        return "SetTimeOfDay {$hour}:{$minute}";
    }

    public static function serverChat($message) {
        return 'ServerChat ' . escapeshellarg($message);
    }

    public static function getChat() {
        return 'GetChat';
    }

    public static function getTribeLog() {
        return 'GetTribeLog';
    }
}