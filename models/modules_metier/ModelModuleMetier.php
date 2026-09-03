<?php

class ModelModuleMetier extends BaseModel
{
    protected string $table = 'modules_metier';
    protected string $primaryKey = 'id_module';
    protected ?string $statusField = 'statut_module';
    protected ?string $createdAtField = 'created_at_module';
}
