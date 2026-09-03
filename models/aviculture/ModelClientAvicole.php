<?php

class ModelClientAvicole extends BaseModel
{
    protected string $table = 'clients_avicoles';
    protected string $primaryKey = 'id_client_avicole';
    protected ?string $statusField = 'statut_client_avicole';
    protected ?string $createdAtField = 'created_at_client_avicole';
}
