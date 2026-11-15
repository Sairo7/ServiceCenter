<?php
/**
 * ConfigModel Class
 * Handles database operations for system configurations (system_configs table)
 */

class ConfigModel
{
    private $db;
    private $table = 'system_configs';

    public function __construct()
    {
        $this->db = new Database();
        $this->ensureTableExists();
    }

    /**
     * Ensure table exists automatically
     */
    private function ensureTableExists()
    {
        if ($this->db->isConnected()) {
            $sql = "CREATE TABLE IF NOT EXISTS " . $this->table . " (
                id INT AUTO_INCREMENT PRIMARY KEY,
                config_key VARCHAR(100) NOT NULL UNIQUE,
                config_value TEXT NULL,
                description VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $this->db->query($sql);
            $this->db->execute();
        }
    }

    /**
     * Default configurations fallback
     */
    public function getDefaults()
    {
        return [
            'service_name'         => 'TKJ Service Center',
            'gateway_provider'     => 'Fonnte',
            'api_token'            => 'jmsDKErQCKjFWhycUYqe',
            'default_country_code' => '62',
            'gemini_api_key'       => getenv('GEMINI_API_KEY') ?: ''
        ];
    }

    /**
     * Get all configurations as associative array key => value
     */
    public function getAllConfigs()
    {
        $configs = $this->getDefaults();

        if ($this->db->isConnected()) {
            $this->db->query("SELECT config_key, config_value FROM " . $this->table);
            $rows = $this->db->resultSet();
            if ($rows) {
                foreach ($rows as $row) {
                    $configs[$row['config_key']] = $row['config_value'];
                }
            }
        }

        return $configs;
    }

    /**
     * Get single configuration value by key
     */
    public function get($key, $default = null)
    {
        $defaults = $this->getDefaults();
        $fallback = $default ?? ($defaults[$key] ?? null);

        if ($this->db->isConnected()) {
            $this->db->query("SELECT config_value FROM " . $this->table . " WHERE config_key = :key LIMIT 1");
            $this->db->bind(':key', $key);
            $row = $this->db->single();
            if ($row !== false && $row !== null) {
                return $row['config_value'];
            }
        }

        return $fallback;
    }

    /**
     * Upsert single configuration key-value
     */
    public function set($key, $value, $description = null)
    {
        if (!$this->db->isConnected()) {
            return false;
        }

        $sql = "INSERT INTO " . $this->table . " (config_key, config_value, description)
                VALUES (:key, :val, :desc)
                ON DUPLICATE KEY UPDATE 
                    config_value = VALUES(config_value),
                    description = COALESCE(VALUES(description), description)";

        $this->db->query($sql);
        $this->db->bind(':key', trim($key));
        $this->db->bind(':val', (string)$value);
        $this->db->bind(':desc', $description);

        return $this->db->execute();
    }

    /**
     * Bulk save configurations array
     */
    public function saveAll(array $data)
    {
        $success = true;
        foreach ($data as $key => $val) {
            if (!$this->set($key, $val)) {
                $success = false;
            }
        }
        return $success;
    }
}

