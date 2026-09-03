<?php

class Context
{
    public static function annee(): string
    {
        return $_SESSION['annee_active_code'] ?? '0GklBk07waYoLB6pHwY';
    }

    public static function etablissement(): string
    {
        return $_SESSION['etablissement_active_code'] ?? '5454544456';
    }

    public static function zone(): ?string
    {
        return $_SESSION['zone_active_code'] ?? null;
    }

    public static function user(): ?string
    {
        return $_SESSION[USERS_AUTH]['code_user'] ?? ($_SESSION['code_user'] ?? null);
    }

    public static function userId(): ?int
    {
        return $_SESSION[USERS_AUTH]['id_user'] ?? null;
    }

    public static function role(): string
    {
        $roles = $_SESSION[USERS_AUTH]['roles'] ?? [];
        if (!empty($roles) && is_array($roles)) {
            return $roles[0];
        }
        return $_SESSION[USERS_AUTH]['role_code'] ?? ($_SESSION['role_code'] ?? 'ROLE_COMMERCIAL');
    }

    public static function isSuperAdmin(): bool
    {
        return in_array(self::role(), ['ROLE_SUPERADMIN', 'ROLE_ADMIN', 'ROLE_DIR_GENERAL'], true);
    }

    public static function isCommercial(): bool
    {
        return self::role() === 'ROLE_COMMERCIAL';
    }

    public static function isGestionnaire(): bool
    {
        return self::role() === 'ROLE_GESTIONNAIRE';
    }

    public static function isFinance(): bool
    {
        return self::role() === 'ROLE_FINANCE';
    }

    public static function isAdmin(): bool
    {
        return self::isSuperAdmin();
    }

    public static function all(): array
    {
        return [
            'annee_code' => self::annee(),
            'etablissement_code' => self::etablissement(),
            'zone_code' => self::zone(),
            'user_code' => self::user(),
            'role_code' => self::role(),
            'is_super_admin' => self::isSuperAdmin(),
        ];
    }

    /**
     * Applique automatiquement le filtrage selon le rôle connecté :
     * - Commercial : Filtré strictement sur user_code + etablissement_code + annee_code
     * - Gestionnaire : Filtré sur etablissement_code + zone_code (si présente) + annee_code
     * - Finance : Filtré sur etablissement_code + annee_code
     * - Admin : Filtré facultativement sur etablissement_code
     */
    public static function applyScopeSQL(string $tableAlias, array &$conditions, array &$params, bool $userFieldAsCommercial = true): void
    {
        $prefix = !empty($tableAlias) ? rtrim($tableAlias, '.') . '.' : '';

        // Établissement
        if (self::etablissement()) {
            $conditions[] = "{$prefix}etablissement_code = ?";
            $params[] = self::etablissement();
        }

        // Si Commercial terrain -> Filtrer obligatoirement sur son code_user
        if (self::isCommercial()) {
            $userCol = $userFieldAsCommercial ? 'user_code' : 'commercial_code';
            $conditions[] = "{$prefix}{$userCol} = ?";
            $params[] = self::user();
        }

        // Si Gestionnaire -> Filtrer par zone si définie
        if (self::isGestionnaire() && self::zone()) {
            $conditions[] = "{$prefix}zone_code = ?";
            $params[] = self::zone();
        }

        // Année d'activité
        if (self::annee()) {
            $conditions[] = "{$prefix}annee_code = ?";
            $params[] = self::annee();
        }
    }

    public static function applyTo(array &$data, array $fields = ['annee_code', 'etablissement_code', 'zone_code', 'user_code']): void
    {
        $map = [
            'annee_code' => self::annee(),
            'etablissement_code' => self::etablissement(),
            'zone_code' => self::zone(),
            'user_code' => self::user(),
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $map) && (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null)) {
                $data[$field] = $map[$field];
            }
        }
    }
}
