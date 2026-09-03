<?php

class ModelFournisseurAvicole extends BaseModel
{
    protected string $table = 'fournisseurs_avicoles';
    protected string $primaryKey = 'id_fournisseur_avicole';
    protected ?string $statusField = 'statut_fournisseur_avicole';
    protected ?string $createdAtField = 'created_at_fournisseur_avicole';
}
