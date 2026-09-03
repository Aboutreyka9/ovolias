<?php

trait PressingAware
{
    protected function getCurrentPressingCode(): ?string
    {
        return null;
    }

    protected function requirePressingAccess(string $pressingCode): void
    {
        return;
    }

    protected function hasActiveAbonnement(?string $pressingCode = null): bool
    {
        return true;
    }

    protected function getActiveAbonnementDetails(?string $pressingCode = null): ?array
    {
        return null;
    }

    protected function requireActiveAbonnement(?string $pressingCode = null, string $actionLabel = ''): void
    {
        return;
    }

    protected function filterByPressing(array $rows, string $pressingField, ?string $pressingCode): array
    {
        return $rows;
    }
}
