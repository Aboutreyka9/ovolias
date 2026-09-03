<?php

class ModelEtablissement extends BaseModel
{
    protected string $table = 'etablissements';
    protected string $primaryKey = 'id_etablissement';
    protected ?string $statusField = 'statut_etablissement';
    protected ?string $createdAtField = 'created_at_etablissement';
}
