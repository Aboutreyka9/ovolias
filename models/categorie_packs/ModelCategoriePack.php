<?php

class ModelCategoriePack extends BaseModel
{
    protected string $table = 'categorie_packs';
    protected string $primaryKey = 'id_categorie_pack';
    protected ?string $statusField = 'statut_categorie_pack';
    protected ?string $createdAtField = 'created_at_categorie_pack';
}
