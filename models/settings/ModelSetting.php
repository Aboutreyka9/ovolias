<?php

class ModelSetting extends BaseModel
{
    protected string $table = 'settings_lavex';
    protected string $primaryKey = 'id_setting';

    public function getSetting(string $key, $default = null)
    {
        try {
            $stmt = $this->getCon()->prepare("SELECT valeur_setting FROM {$this->table} WHERE cle_setting = ? LIMIT 1");
            $stmt->execute([$key]);
            $val = $stmt->fetchColumn();
            return $val !== false ? $val : $default;
        } catch (Exception $e) {
            error_log('[ModelSetting::getSetting] ' . $e->getMessage());
            return $default;
        }
    }

    public function setSetting(string $key, $value): bool
    {
        try {
            $stmt = $this->getCon()->prepare("
                INSERT INTO {$this->table} (cle_setting, valeur_setting) 
                VALUES (:key, :val) 
                ON DUPLICATE KEY UPDATE valeur_setting = :val
            ");
            return $stmt->execute([':key' => $key, ':val' => (string)$value]);
        } catch (Exception $e) {
            error_log('[ModelSetting::setSetting] ' . $e->getMessage());
            return false;
        }
    }

    public function getAllSettings(): array
    {
        try {
            $stmt = $this->getCon()->query("SELECT * FROM {$this->table} ORDER BY id_setting ASC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $settings = [];
            foreach ($rows as $r) {
                $settings[$r['cle_setting']] = $r['valeur_setting'];
            }
            return $settings;
        } catch (Exception $e) {
            error_log('[ModelSetting::getAllSettings] ' . $e->getMessage());
            return [];
        }
    }
}
