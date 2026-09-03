<?php

class ModelAviculture extends BaseModel
{
    protected string $table = 'pesees_etiquettes_avicole';
    protected string $primaryKey = 'id_pesee';
    protected ?string $statusField = 'statut_pesee';
    protected ?string $createdAtField = 'created_at_pesee';
}
