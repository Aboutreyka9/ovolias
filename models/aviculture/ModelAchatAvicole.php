<?php

class ModelAchatAvicole extends BaseModel
{
    protected string $table = 'achats_avicoles';
    protected string $primaryKey = 'id_achat_avicole';
    protected ?string $statusField = 'statut_reglement';
    protected ?string $createdAtField = 'created_at_achat';
}
