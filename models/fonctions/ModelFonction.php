<?php

class ModelFonction extends BaseModel
{
    protected string $table = 'fonctions';
    protected string $primaryKey = 'id_fonction';
    protected ?string $statusField = 'statut_fonction';
}
