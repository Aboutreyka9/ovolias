<?php

class ModelTypeDepense extends BaseModel
{
    protected string $table = 'type_depenses';
    protected string $primaryKey = 'id_type_depense';
    protected ?string $statusField = null;
    protected ?string $createdAtField = null;
}
