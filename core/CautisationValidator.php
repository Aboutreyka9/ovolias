<?php

/**
 * Classe utilitaire pour la gestion des calculs et validations de cautisations
 */
class CautisationValidator
{
    /**
     * Valide si un montant est un multiple valide du prix de cotisation
     * 
     * @param float $montant Le montant à valider
     * @param float $prixCotisation Le prix de cotisation par jour
     * @return array ['valid' => bool, 'message' => string, 'suggested_amount' => float]
     */
    public static function validateAmount(float $montant, float $prixCotisation): array
    {
        if ($montant <= 0) {
            return [
                'valid' => false,
                'message' => 'Le montant doit être supérieur à 0'
            ];
        }

        if ($prixCotisation <= 0) {
            return [
                'valid' => false,
                'message' => 'Prix de cotisation invalide'
            ];
        }

        // Vérifier si le montant est un multiple exact
        $remainder = fmod($montant, $prixCotisation);
        
        // Tolérance pour les arrondis de point flottant
        if (abs($remainder) > 0.01 && abs($remainder - $prixCotisation) > 0.01) {
            $suggestedAmount = floor($montant / $prixCotisation) * $prixCotisation;
            return [
                'valid' => false,
                'message' => 'Le montant doit être un multiple de ' . number_format($prixCotisation, 2, ',', ' ') . ' FCFA',
                'suggested_amount' => $suggestedAmount > 0 ? $suggestedAmount : $prixCotisation
            ];
        }

        return ['valid' => true, 'message' => 'OK'];
    }

    /**
     * Valide si le montant n'excède pas le montant restant
     */
    public static function validateAmountNotExceeds(float $montant, float $montantRestant): array
    {
        if ($montant > $montantRestant) {
            return [
                'valid' => false,
                'message' => 'Le montant dépasse le montant restant à payer (' . 
                    number_format($montantRestant, 2, ',', ' ') . ' FCFA)'
            ];
        }
        return ['valid' => true, 'message' => 'OK'];
    }

    /**
     * Valide si le nombre de jours n'excède pas le nombre de jours restant
     */
    public static function validateDaysNotExceeds(int $jours, int $joursRestants): array
    {
        if ($jours > $joursRestants) {
            return [
                'valid' => false,
                'message' => 'Le nombre de jours dépasse le nombre de jours restants (' . 
                    $joursRestants . ' jours)'
            ];
        }
        return ['valid' => true, 'message' => 'OK'];
    }

    /**
     * Calcule le montant à partir du nombre de jours
     */
    public static function calculateAmount(int $nombreJours, float $prixCotisation): float
    {
        return (float)$nombreJours * $prixCotisation;
    }

    /**
     * Calcule le nombre de jours à partir du montant
     */
    public static function calculateDays(float $montant, float $prixCotisation): int
    {
        if ($prixCotisation <= 0) {
            return 0;
        }
        return (int)floor($montant / $prixCotisation);
    }

    /**
     * Calcule la date du prochain rendez-vous
     */
    public static function calculateNextDate(int $numberOfDays, ?\DateTime $fromDate = null): string
    {
        if ($fromDate === null) {
            $fromDate = new \DateTime('now');
        }

        $nextDate = clone $fromDate;
        $nextDate->add(new \DateInterval('P' . $numberOfDays . 'D'));
        
        return $nextDate->format('d/m/Y');
    }

    /**
     * Calcule la date du prochain rendez-vous au format datetime
     */
    public static function calculateNextDatetime(int $numberOfDays, ?\DateTime $fromDate = null): \DateTime
    {
        if ($fromDate === null) {
            $fromDate = new \DateTime('now');
        }

        $nextDate = clone $fromDate;
        $nextDate->add(new \DateInterval('P' . $numberOfDays . 'D'));
        
        return $nextDate;
    }

    /**
     * Valide les données complètes du paiement
     */
    public static function validatePaymentData(array $data): array
    {
        $errors = [];

        // Vérifier la souscription
        if (empty($data['code_souscription'])) {
            $errors[] = 'Code souscription requis';
        }

        // Vérifier le mode de paiement
        if (empty($data['mode_paiement'])) {
            $errors[] = 'Mode de paiement requis';
        } elseif (!in_array($data['mode_paiement'], ['especes', 'mobile_money', 'cheque', 'virement'])) {
            $errors[] = 'Mode de paiement invalide';
        }

        // Vérifier le type de paiement
        if (empty($data['type_paiement'])) {
            $errors[] = 'Type de paiement requis';
        } elseif (!in_array($data['type_paiement'], ['montant', 'jours'])) {
            $errors[] = 'Type de paiement invalide';
        }

        // Vérifier montant ou jours selon le type
        $montant = (float)($data['montant'] ?? 0);
        $jours = (int)($data['nombre_jours'] ?? 0);

        if ($data['type_paiement'] === 'montant') {
            if ($montant <= 0) {
                $errors[] = 'Montant invalide';
            }
        } else {
            if ($jours <= 0) {
                $errors[] = 'Nombre de jours invalide';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Formate un montant en devise FCFA
     */
    public static function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Génère un code unique pour la cautisation
     */
    public static function generateCode(string $prefix = 'CAUT-'): string
    {
        return $prefix . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
    }
}
