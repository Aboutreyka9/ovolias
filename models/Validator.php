<?php

class Validator
{
    private $pdo;
    private $secretKey = 'TaCleSecrete';

    public function __construct()
    {
        $this->pdo = new Database();
    }

    public function safeRollBack()
    {
        if ($this->pdo->getCon()->inTransaction()) {
            $this->pdo->getCon()->rollBack();
        }
    }

    public function safeCommit()
    {
        if ($this->pdo->getCon()->inTransaction()) {
            $this->pdo->getCon()->commit();
        }
    }

    public function safeBeginTransaction()
    {
        if (!$this->pdo->getCon()->inTransaction()) {
            $this->pdo->getCon()->beginTransaction();
        }
    }

    public function hasRole($role)
    {
        return in_array($role, $_SESSION['roles'] ?? []);
    }

    public function verif($table, $field, $value)
    {
        $result = false;
        try {
            $sql = "SELECT * FROM $table WHERE $field=?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$value]);
            if ($query->rowCount() > 0) {
                $result = true;
            }
        } catch (Exception $e) {
            error_log("Validator::verif error: " . $e->getMessage());
        }

        return $result;
    }

    public function _verif($table, $field, $value, $id, $id_val)
    {
        $result = false;
        try {
            $sql = "SELECT * FROM $table WHERE $field=? AND $id != ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$value, $id_val]);
            if ($query->rowCount() > 0) {
                $result = true;
            }
        } catch (Exception $e) {
            error_log("Validator::_verif error: " . $e->getMessage());
        }

        return $result;
    }

    public function verifs($table, $field1, $field2, $val1, $val2)
    {
        $data = false;
        try {
            $sql = "SELECT * FROM $table WHERE $field1=? AND $field2=?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$val1, $val2]);
            if ($query->rowCount() > 0) {
                $data = true;
            }
        } catch (Exception $e) {
            error_log("Validator::verifs error: " . $e->getMessage());
        }

        return $data;
    }

    public function _verifs($table, $field1, $field2, $val1, $val2, $session, $sess_val)
    {
        $data = false;
        try {
            $sql = "SELECT * FROM $table WHERE $field1=? AND $field2=? AND $session=?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$val1, $val2, $sess_val]);
            if ($query->rowCount() > 0) {
                $data = true;
            }
        } catch (Exception $e) {
            error_log("Validator::_verifs error: " . $e->getMessage());
        }

        return $data;
    }

    public function updateByElement($table, $element, $element_val, $id, $id_val)
    {
        try {
            $sql = "UPDATE $table SET $element=? WHERE $id=?";
            $query = $this->pdo->getCon()->prepare($sql);
            if ($query->execute([$element_val, $id_val])) {
                return true;
            }
        } catch (Exception $e) {
            error_log("Validator::updateByElement error: " . $e->getMessage());
        }

        return false;
    }

    public function getByElement($table, $el, $val)
    {
        $data = '';
        try {
            $sql = "SELECT * FROM $table WHERE $el = ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$val]);
            if ($query->rowCount() > 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Validator::getByElement error: " . $e->getMessage());
        }

        return $data;
    }

    public function getByElements($table, $el1, $val1, $el2, $val2)
    {
        $data = '';
        try {
            $sql = "SELECT * FROM $table WHERE $el1 = ? AND $el2 = ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$val1, $val2]);
            if ($query->rowCount() > 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Validator::getByElements error: " . $e->getMessage());
        }

        return $data;
    }

    public function getAll($table)
    {
        $data = '';
        try {
            $sql = "SELECT * FROM $table";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute();
            if ($query->rowCount() > 0) {
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Validator::getAll error: " . $e->getMessage());
        }

        return $data;
    }

    public function getAllOder($table, $el)
    {
        $data = '';
        try {
            $sql = "SELECT * FROM $table ORDER BY $el DESC";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute();
            if ($query->rowCount() > 0) {
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Validator::getAllOder error: " . $e->getMessage());
        }

        return $data;
    }

    public function getTable($table)
    {
        $data = '';
        try {
            $sql = "SELECT * FROM $table";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute();
            if ($query->rowCount() > 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Validator::getTable error: " . $e->getMessage());
        }

        return $data;
    }

    public function getAllByElement($table, $el, $val)
    {
        $data = [];
        try {
            $sql = "SELECT * FROM $table WHERE $el = ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$val]);
            if ($query->rowCount() > 0) {
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Validator::getAllByElement error: " . $e->getMessage());
        }

        return $data;
    }

    public function getByMaxElement($table, $id)
    {
        $data = [];
        try {
            $sql = "SELECT MAX($id) AS $id FROM $table LIMIT 1";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute();
            if ($query->rowCount() > 0) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Validator::getByMaxElement error: " . $e->getMessage());
        }

        return $data;
    }

    public function getAllByElements($table, $el1, $el2, $val1, $val2)
    {
        $data = [];
        try {
            $sql = "SELECT * FROM $table WHERE $el1 = ? AND $el2 = ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$val1, $val2]);
            if ($query->rowCount() > 0) {
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Validator::getAllByElements error: " . $e->getMessage());
        }

        return $data;
    }

    public static function validNumber($number, $limit)
    {
        return (ctype_digit((string)$number) && strlen((string)$number) === $limit);
    }

    public static function afficherImageBLOB($blob, $defaultImg, $typeMime = 'image/png')
    {
        if (empty($blob)) {
            return RACINE . $defaultImg;
        }
        return 'data:' . $typeMime . ';base64,' . base64_encode($blob);
    }

    public static function traiterImageUpload(string $champ = 'photo', int $tailleMax = 2 * 1024 * 1024): ?string
    {
        if (!isset($_FILES[$champ]) || $_FILES[$champ]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $fichier = $_FILES[$champ];
        $nomTmp = $fichier['tmp_name'];
        $taille = $fichier['size'];
        $typeMime = mime_content_type($nomTmp);

        $extensionsAutorisees = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if ($taille > $tailleMax) {
            throw new RuntimeException("L'image dépasse la taille maximale autorisée de 2 Mo.");
        }

        if (!array_key_exists($typeMime, $extensionsAutorisees)) {
            throw new RuntimeException("Format d'image non autorisé. Formats acceptés : JPG, PNG, WEBP.");
        }

        $binaire = file_get_contents($nomTmp);
        if ($binaire === false) {
            throw new RuntimeException("Erreur lors de la lecture de l'image.");
        }

        return $binaire;
    }

    public static function dateActuelle($waveDate = null)
    {
        if ($waveDate) {
            try {
                $dateTimeWave = new \DateTime($waveDate, new \DateTimeZone('UTC'));
                $dateTimeWave->setTimezone(new \DateTimeZone('Africa/Abidjan'));
                return $dateTimeWave->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return 'Invalid date: ' . $waveDate;
            }
        } else {
            $date = new \DateTime('now', new \DateTimeZone('Africa/Abidjan'));
            return $date->format('Y-m-d H:i:s');
        }
    }

    public function generateCode($table, $field, $prefixe, $length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomCode = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomCode .= $characters[rand(0, $charactersLength - 1)];
        }
        if ($this->verif($table, $field, $randomCode)) {
            return $this->generateCode($table, $field, $prefixe, $length);
        }

        return $prefixe . $randomCode;
    }

    public function processUploadedFile($file, $destinationDir, $allowedTypes = [], $maxSize = 5000000)
    {
        if ($file['error'] !== 0) {
            return "Erreur lors de l'upload du fichier. Code d'erreur : " . $file['error'];
        }

        if ($file['size'] > $maxSize) {
            return 'Le fichier est trop volumineux. La taille maximale autorisée est de ' . $maxSize / 1000000 . ' MB.';
        }

        if (!in_array($file['type'], $allowedTypes)) {
            return 'Type de fichier non autorisé. Les types acceptés sont : ' . implode(', ', $allowedTypes);
        }

        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExt;
        $filePath = $destinationDir . '/' . $fileName;

        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $filePath;
        } else {
            return 'Erreur lors du déplacement du fichier.';
        }
    }

    public static function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }

        $data = trim((string)$data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

        return $data;
    }

    public static function validateRequiredFields(array $data, array $optionalFields = [])
    {
        $errors = [];

        foreach ($data as $key => $value) {
            if (!in_array($key, $optionalFields) && empty(trim((string)$value))) {
                $errors[$key] = "Le champ '$key' est requis et ne peut pas être vide.";
            }
        }

        return empty($errors) ? true : $errors;
    }

    public static function trColor($i)
    {
        return ($i % 2 == 0) ? '#ffffff' : '#D8D2D2FF';
    }

    public static function legendStatus($iconActive, $text1, $iconInactive, $text2)
    {
        return '
        <div style="margin-bottom:-90px">
            <strong>Légende :</strong>
            <span class="badge" style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 10px;">
                <i data-lucide="' . $iconActive . '"></i> ' . $text1 . '
            </span>
            <span class="badge" style="background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 10px;">
                <i data-lucide="' . $iconInactive . '"></i> ' . $text2 . '
            </span>
        </div>';
    }

    public static function viewStatus($icon1, $icon2, $status, $nb)
    {
        $isActive = $status == $nb;
        $statut = $isActive ? '<i data-lucide="' . $icon1 . '"></i>' : '<i data-lucide="' . $icon2 . '"></i>';
        $bgColor = $isActive ? '#28a745' : '#dc3545';
        return "<span class='badge' style='font-size:10px; background-color: $bgColor; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; text-transform: uppercase;'>$statut</span>";
    }

    public static function viewStatus2($icon1, $icon2, $icon3, $status, $ref)
    {
        $isActive = $status;
        if ($isActive < $ref) {
            $statut = ' <span class="badge badge-warning"> <i data-lucide="' . $icon1 . '"></i> En cours</span>';
        }
        if ($isActive > $ref) {
            $statut = ' <span class="badge badge-danger"><i data-lucide="' . $icon2 . '"> </i> Echec</span> ';
        }
        if ($isActive == $ref) {
            $statut = '  <span class="badge badge-success"><i data-lucide="' . $icon3 . '"> </i> Succès</span>';
        }
        return "$statut";
    }

    public static function icon($icon)
    {
        return '<i data-lucide="' . $icon . '"></i>';
    }

    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken(?string $token): bool
    {
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        if (!isset($_SESSION['csrf_token_time']) || (time() - $_SESSION['csrf_token_time']) > 3600) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], (string)$token);
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::generateCsrfToken()) . '">';
    }

    public function getCountByElements($table, $el1, $el2, $val1, $val2)
    {
        try {
            $sql = "SELECT COUNT(*) total FROM $table ar WHERE ar.$el1 = ? AND ar.$el2 = ? ";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$val1, $val2]);
            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
                return $result['total'];
            }
            return 0;
        } catch (\Exception $e) {
            error_log("Validator::getCountByElements error: " . $e->getMessage());
            return 0;
        }
    }

    public function getCountByJoinElement($table1, $table2, $joinCondition, $element1, $element2, $value1, $value2)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM $table1 t1 INNER JOIN $table2 t2 ON $joinCondition WHERE $element1 = ? AND $element2 = ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$value1, $value2]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['total'] : 0;
        } catch (\Exception $e) {
            error_log("Validator::getCountByJoinElement error: " . $e->getMessage());
            return 0;
        }
    }

    public function getByJoinElement($table1, $table2, $joinCondition, $element1, $element2, $value1, $value2)
    {
        try {
            $sql = "SELECT * FROM $table1 t1 INNER JOIN $table2 t2 ON $joinCondition WHERE $element1 = ? AND $element2 = ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$value1, $value2]);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return !empty($result) ? $result : null;
        } catch (\Exception $e) {
            error_log("Validator::getByJoinElement error: " . $e->getMessage());
            return null;
        }
    }

    public function getCountByElement($table, $el1, $el2, $val1, $val2)
    {
        try {
            $sql = "SELECT COUNT(*) total FROM $table ar WHERE ar.$el1 = ? AND ar.$el2 = ? ";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$val1, $val2]);
            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
                return $result['total'];
            }
            return 0;
        } catch (\Exception $e) {
            error_log("Validator::getCountByElement error: " . $e->getMessage());
            return 0;
        }
    }

    public function getCountTable($table)
    {
        try {
            $sql = "SELECT COUNT(*) total FROM $table";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute();
            if ($query->rowCount() > 0) {
                $result = $query->fetch(PDO::FETCH_ASSOC);
                return $result['total'];
            }
            return 0;
        } catch (\Exception $e) {
            error_log("Validator::getCountTable error: " . $e->getMessage());
            return 0;
        }
    }

    public function updateByElements($table, $element1, $element_val1, $element2, $element_val2, $id, $id_val)
    {
        try {
            $sql = "UPDATE $table SET $element1=? WHERE $element2=? AND $id=?";
            $query = $this->pdo->getCon()->prepare($sql);
            return $query->execute([$element_val1, $element_val2, $id_val]);
        } catch (Exception $e) {
            error_log("Validator::updateByElements error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Chiffre une valeur en URL-Safe Base64 (100% sans /, +, =, :)
     */
    public function crypter($data)
    {
        $key = hash('sha256', $this->secretKey, true);
        $iv = openssl_random_pseudo_bytes(16);
        $rawEncrypted = openssl_encrypt((string)$data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $rawEncrypted, $key, true);
        $combined = $iv . $hmac . $rawEncrypted;
        return rtrim(strtr(base64_encode($combined), '+/', '-_'), '=');
    }

    /**
     * Déchiffre une valeur chiffrée (compatible URL-Safe, standard Base64 et entiers directs)
     */
    public function decrypter($encryptedData)
    {
        if (empty($encryptedData)) {
            return null;
        }
        if (is_numeric($encryptedData)) {
            return (int)$encryptedData;
        }

        $rawInput = (string)$encryptedData;
        $b64 = strtr($rawInput, '-_', '+/');
        $pad = strlen($b64) % 4;
        if ($pad > 0) {
            $b64 .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode($b64, true);

        $key = hash('sha256', $this->secretKey, true);

        // Format binaire URL-Safe moderne (16 octets IV + 32 octets HMAC + chiffrement)
        if ($decoded !== false && strlen($decoded) > 48) {
            $iv = substr($decoded, 0, 16);
            $hmac = substr($decoded, 16, 32);
            $cipherText = substr($decoded, 48);
            
            $calcHmac = hash_hmac('sha256', $cipherText, $key, true);
            if (hash_equals($hmac, $calcHmac)) {
                $dec = openssl_decrypt($cipherText, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
                if ($dec !== false) {
                    return is_numeric($dec) ? (int)$dec : $dec;
                }
            }
        }

        // Rétrocompatibilité avec l'ancien format séparé par '::'
        $legacyString = ($decoded !== false) ? $decoded : $rawInput;
        if (strpos($legacyString, '::') !== false) {
            $parts = explode('::', $legacyString, 3);
            if (count($parts) === 3) {
                list($cipherText, $iv, $hmac) = $parts;
                $keyHex = hash('sha256', $this->secretKey);
                if (hash_hmac('sha256', $cipherText, $keyHex) === $hmac) {
                    $dec = openssl_decrypt($cipherText, 'AES-256-CBC', $keyHex, 0, $iv);
                    if ($dec !== false) {
                        return is_numeric($dec) ? (int)$dec : $dec;
                    }
                }
            }
        }

        return is_numeric($rawInput) ? (int)$rawInput : $rawInput;
    }

    public static function dateHier()
    {
        $date = date('Y-m-d');
        return date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }

    public static function dateActuelleCourt()
    {
        $date = new \DateTime();
        return $date->format('Y-m-d');
    }

    public static function dateToInteger($dateToconvert)
    {
        $date = new DateTime($dateToconvert);
        return $date->getTimestamp();
    }

    public static function integerToDate($integerToconvert)
    {
        if ($integerToconvert) {
            $date = new DateTime();
            $date->setTimestamp((int) $integerToconvert);
            return $date->format('Y-m-d');
        }
        return null;
    }

    public function isValidMoney($amount, $amountMax)
    {
        if (preg_match('/^[0-9]+$/', $amount) && intval($amount) >= $amountMax && intval($amount) % $amountMax === 0) {
            return true;
        }
        return false;
    }

    public static function calculerDateFin($dateDebut, $nombreMois)
    {
        if (!empty($dateDebut) && is_numeric($nombreMois) && $nombreMois > 0) {
            $date = new DateTime($dateDebut);
            $date->modify("+{$nombreMois} months");
            return $date->format('Y-m-d');
        }
        return null;
    }

    public static function formatDate($date)
    {
        if (!$date || $date == '0000-00-00') {
            return 'Date invalide';
        }
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj) {
            return 'Format incorrect';
        }
        return $dateObj->format('d-m-Y');
    }

    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isConnected(): bool
    {
        return isset($_SESSION[USERS_AUTH]['code_user']) && !empty($_SESSION[USERS_AUTH]['code_user']);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }

    public function getSumAndCount2($table, $id, $id_val, $status, $status_val, $etat, $etat_val, $el)
    {
        try {
            $sql = "SELECT SUM($el) somme, COUNT($el) count FROM $table v WHERE v.$id = ? AND v.$status = ? AND v.$etat = ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$id_val, $status_val, $etat_val]);
            if ($query->rowCount() > 0) {
                return $query->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (\Exception $e) {
            error_log("Validator::getSumAndCount2 error: " . $e->getMessage());
            return null;
        }
    }

    public function getSumAndCount($table, $id, $id_val, $status, $status_val, $el)
    {
        try {
            $sql = "SELECT SUM($el) somme, COUNT($el) count FROM $table v WHERE v.$id = ? AND v.$status = ?";
            $query = $this->pdo->getCon()->prepare($sql);
            $query->execute([$id_val, $status_val]);
            if ($query->rowCount() > 0) {
                return $query->fetch(PDO::FETCH_ASSOC);
            }
            return null;
        } catch (\Exception $e) {
            error_log("Validator::getSumAndCount error: " . $e->getMessage());
            return null;
        }
    }

    public static function saveSesion(string $entity, $data)
    {
        if (is_array($data)) {
            if (sizeof($data) > 0 && !empty($entity)) {
                foreach ($data as $key => $value) {
                    $_SESSION[$entity][$key] = $value;
                }
            }
        } else {
            $_SESSION[$entity] = $data;
        }
    }

    public function insert(string $table, array $data, array $keys): bool
    {
        try {
            $columns = implode(', ', $keys);
            $placeholders = implode(', ', array_map(fn($k) => ":$k", $keys));
            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
            $stmt = $this->pdo->getCon()->prepare($sql);
            return $stmt->execute($data);
        } catch (Exception $e) {
            error_log("Insert error: " . $e->getMessage());
            return false;
        }
    }

    public function update(string $table, array $data, array $keys, string $idField, int $id): bool
    {
        try {
            $setClause = implode(', ', array_map(fn($k) => "$k = :$k", $keys));
            $sql = "UPDATE $table SET $setClause WHERE $idField = :id";
            $stmt = $this->pdo->getCon()->prepare($sql);
            $data['id'] = $id;
            return $stmt->execute($data);
        } catch (Exception $e) {
            error_log("Update error: " . $e->getMessage());
            return false;
        }
    }

    public static function getSesion(string $entity, string $field = "")
    {
        if (!empty($field) && !empty($entity)) {
            if (isset($_SESSION[$entity]) && key_exists($field, $_SESSION[$entity])) {
                return $_SESSION[$entity][$field];
            } else {
                if (isset($_SESSION[$entity])) {
                    return $_SESSION[$entity];
                }
            }
        }
        return null;
    }

    /**
     * Nettoie et formate un numéro de téléphone pour retirer systématiquement l'indicatif +225 ou 225
     * Retourne toujours un numéro national de 10 chiffres (ex: 0701020304)
     */
    public static function cleanPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        // Enlever tous les caractères non numériques
        $cleaned = preg_replace('/[^0-9]/', '', (string)$phone);
        
        // Si commence par 00225, enlever les 5 premiers chiffres
        if (substr($cleaned, 0, 5) === '00225') {
            $cleaned = substr($cleaned, 5);
        }
        // Si commence par 225 et fait plus de 10 chiffres (ex: 2250701020304)
        elseif (substr($cleaned, 0, 3) === '225' && strlen($cleaned) > 10) {
            $cleaned = substr($cleaned, 3);
        }

        return trim($cleaned);
    }

    /**
     * Valide qu'un numéro de téléphone fait exactement 10 chiffres (standard ivoirien)
     */
    public static function validPhone(?string $phone): bool
    {
        $cleaned = self::cleanPhone($phone);
        return (bool)preg_match('/^[0-9]{10}$/', $cleaned);
    }
}

