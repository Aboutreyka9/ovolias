<?php

class ModelPermission extends BaseModel
{
    protected string $table = 'permissions';
    protected string $primaryKey = 'id_permission';
    protected ?string $statusField = 'statut_permission';

    public function getGrouped(): array
    {
        try {
            $sql = "SELECT * FROM permissions WHERE statut_permission = 'actif' ORDER BY module_permission ASC, id_permission ASC";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $groups = [];
            foreach ($items as $item) {
                $group = !empty($item['module_permission']) ? $item['module_permission'] : 'GÉNÉRAL';
                $groups[$group][] = $item;
            }

            return $groups;
        } catch (Exception $e) {
            error_log('[ModelPermission::getGrouped] ' . $e->getMessage());
            return [];
        }
    }
}
