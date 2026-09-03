<?php

class ModelSession extends BaseModel
{
    protected string $table = 'sessions';
    protected string $primaryKey = 'id_session';
    protected ?string $statusField = 'statut_session';
    protected ?string $createdAtField = 'created_at_session';
}
