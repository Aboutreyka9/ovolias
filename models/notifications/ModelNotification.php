<?php

class ModelNotification extends BaseModel
{
    protected string $table = 'messages';
    protected string $primaryKey = 'id_message';

    /**
     * Récupère les notifications filtrées selon le rôle (Livreur, Pressing ou Super Admin)
     */
    public function getAllWithClient(?string $pressingCode = null, ?string $livreurCode = null, int $limit = 500): array
    {
        try {
            $stmt = $this->getCon()->prepare("SELECT * FROM messages ORDER BY id_message DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Statistiques des notifications filtrées par rôle
     */
    public function getStats(?string $pressingCode = null, ?string $livreurCode = null): array
    {
        try {
            $stmt = $this->getCon()->query("SELECT COUNT(*) as total FROM messages WHERE statut_message = 'en_attente'");
            $count = (int)($stmt ? $stmt->fetchColumn() : 0);
            return [
                'total' => $count,
                'non_lues' => $count,
                'lues' => 0,
                'commandes' => 0,
                'alertes' => 0
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'non_lues' => 0,
                'lues' => 0,
                'commandes' => 0,
                'alertes' => 0
            ];
        }
    }

    /**
     * Marque une notification spécifique comme lue
     */
    public function markAsRead(int $id): bool
    {
        try {
            $stmt = $this->getCon()->prepare("UPDATE {$this->table} SET lu_notification = 1 WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log('[ModelNotification::markAsRead] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bascule le statut lu/non lu d'une notification
     */
    public function toggleRead(int $id): bool
    {
        try {
            $stmt = $this->getCon()->prepare("
                UPDATE {$this->table} 
                SET lu_notification = CASE WHEN lu_notification = 1 THEN 0 ELSE 1 END 
                WHERE {$this->primaryKey} = ?
            ");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log('[ModelNotification::toggleRead] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Marque toutes les notifications comme lues (filtré pour le livreur si applicable)
     */
    public function markAllAsRead(?string $pressingCode = null, ?string $livreurCode = null): bool
    {
        try {
            if ($livreurCode !== null && $livreurCode !== '') {
                $stmt = $this->getCon()->prepare("
                    UPDATE {$this->table} 
                    SET lu_notification = 1 
                    WHERE lu_notification = 0 
                      AND livreur_code = :livreurCode
                ");
                return $stmt->execute([':livreurCode' => $livreurCode]);
            }

            return $this->getCon()->exec("UPDATE {$this->table} SET lu_notification = 1 WHERE lu_notification = 0") !== false;
        } catch (Exception $e) {
            error_log('[ModelNotification::markAllAsRead] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime une notification
     */
    public function deleteNotification(int $id): bool
    {
        try {
            $stmt = $this->getCon()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log('[ModelNotification::deleteNotification] ' . $e->getMessage());
            return false;
        }
    }
}
