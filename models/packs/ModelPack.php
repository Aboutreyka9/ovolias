<?php

class ModelPack extends BaseModel
{
    protected string $table = 'packs';
    protected string $primaryKey = 'id_pack';
    protected ?string $statusField = 'statut_pack';
    protected ?string $createdAtField = 'created_at_pack';

    public function getArticles(string $packCode): array
    {
        try {
            $sql = "
                SELECT pa.*, a.libelle_article
                FROM pack_articles pa
                LEFT JOIN articles a ON a.code_article = pa.article_code
                WHERE pa.pack_code = ?
            ";
            $stmt = $this->getCon()->prepare($sql);
            $stmt->execute([$packCode]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("ModelPack::getArticles error: " . $e->getMessage());
            return [];
        }
    }

    public function syncArticles(string $packCode, array $articlesData, string $anneeCode = '0GklBk07waYoLB6pHwY', string $etabCode = '5454544456'): bool
    {
        try {
            $this->getCon()->beginTransaction();
            
            // Purger les anciens articles du pack
            $stmtDel = $this->getCon()->prepare("DELETE FROM pack_articles WHERE pack_code = ?");
            $stmtDel->execute([$packCode]);

            // Réinsérer les nouveaux articles
            $stmtIns = $this->getCon()->prepare("
                INSERT INTO pack_articles (pack_code, article_code, quantite_article, annee_code, etablissement_code, created_at_pack_article)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $now = date('Y-m-d H:i:s');
            foreach ($articlesData as $item) {
                if (!empty($item['article_code']) && !empty($item['quantite_article'])) {
                    $stmtIns->execute([
                        $packCode,
                        $item['article_code'],
                        (int)$item['quantite_article'],
                        $anneeCode,
                        $etabCode,
                        $now
                    ]);
                }
            }

            $this->getCon()->commit();
            return true;
        } catch (Exception $e) {
            if ($this->getCon()->inTransaction()) {
                $this->getCon()->rollBack();
            }
            error_log("ModelPack::syncArticles error: " . $e->getMessage());
            return false;
        }
    }
}
