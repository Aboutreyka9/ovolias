<?php

class ModelZone extends BaseModel
{
    protected string $table = 'zones';
    protected string $primaryKey = 'id_zone';
    protected ?string $statusField = 'statut_zone';
    protected ?string $createdAtField = 'created_at_zone';
}
