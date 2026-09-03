<?php

class ModelCategorieArticle extends BaseModel
{
    protected string $table = 'categories_articles';
    protected string $primaryKey = 'id_categorie_article';
    protected ?string $statusField = 'statut_categorie_article';
    protected ?string $createdAtField = 'created_at_categorie_article';
}
