<?php

class ModelVenteAvicole extends BaseModel
{
    protected string $table = 'ventes_avicoles';
    protected string $primaryKey = 'id_vente_avicole';
    protected ?string $statusField = 'statut_vente';
    protected ?string $createdAtField = 'created_at_vente';
}
