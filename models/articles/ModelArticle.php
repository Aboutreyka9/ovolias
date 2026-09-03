<?php

class ModelArticle extends BaseModel
{
    protected string $table = 'articles';
    protected string $primaryKey = 'id_article';
    protected ?string $statusField = 'statut_article';
    protected ?string $createdAtField = 'created_at_article';
}
