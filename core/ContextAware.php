<?php

trait ContextAware
{
    protected function applyContextFilters(string $sql, string $alias = 'p', array $extraConditions = []): string
    {
        $conditions = [];
        $params = [];

        $anneeCode = Context::annee();
        $zoneCode = Context::zone();
        $etabCode = Context::etablissement();

        if ($anneeCode !== '0GklBk07waYoLB6pHwY') {
            $conditions[] = "$alias.annee_code = ?";
            $params[] = $anneeCode;
        }

        if ($zoneCode !== null && $zoneCode !== '') {
            $conditions[] = "$alias.zone_code = ?";
            $params[] = $zoneCode;
        }

        if ($etabCode !== null && $etabCode !== '') {
            $conditions[] = "$alias.etablissement_code = ?";
            $params[] = $etabCode;
        }

        foreach ($extraConditions as $condition) {
            $conditions[] = $condition;
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        return $sql;
    }

    protected function contextParams(array $extraParams = []): array
    {
        $params = [];

        $anneeCode = Context::annee();
        $zoneCode = Context::zone();
        $etabCode = Context::etablissement();

        if ($anneeCode !== '0GklBk07waYoLB6pHwY') {
            $params[] = $anneeCode;
        }

        if ($zoneCode !== null && $zoneCode !== '') {
            $params[] = $zoneCode;
        }

        if ($etabCode !== null && $etabCode !== '') {
            $params[] = $etabCode;
        }

        return array_merge($params, $extraParams);
    }

    protected function injectContext(array $data, array $fields = ['annee_code', 'etablissement_code', 'zone_code', 'user_code']): array
    {
        Context::applyTo($data, $fields);
        return $data;
    }
}
