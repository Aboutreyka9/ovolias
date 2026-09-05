<?php

class HomeController extends BaseController
{
    protected function resolveModel()
    {
        return new ModelHome();
    }

    public function index()
    {
        if (Validator::isConnected()) {
            $model = $this->resolveModel();
            $auth = $_SESSION[USERS_AUTH] ?? [];
            $roleCode = $auth['role_code'] ?? '';
            $userCode = $auth['code_user'] ?? '';
            $anneeCode = $_SESSION['annee_active_code'] ?? null;

            $stats = $model->getStats($anneeCode, $userCode, $roleCode);
            $recentVentesAvicoles = $model->getRecentVentesAvicoles(5, $userCode, $roleCode);
            $recentPeseesAvicoles = $model->getRecentPeseesAvicoles(5);
            $recentAchatsAvicoles = $model->getRecentAchatsAvicoles(5);
            $recentDepenses = $model->getRecentDepenses(5);

            $this->loadView('../views/home/index.php', [
                'stats' => $stats,
                'roleCode' => $roleCode,
                'userCode' => $userCode,
                'auth' => $auth,
                'recentVentesAvicoles' => $recentVentesAvicoles,
                'recentPeseesAvicoles' => $recentPeseesAvicoles,
                'recentAchatsAvicoles' => $recentAchatsAvicoles,
                'recentDepenses' => $recentDepenses
            ]);
        } else {
            $this->render('../views/users/connexion.php', [], 'guest');
        }
    }

    public function dashboardData()
    {
        $this->requireAuth();
        $model = $this->resolveModel();
        $auth = $_SESSION[USERS_AUTH] ?? [];
        $roleCode = $auth['role_code'] ?? '';
        $userCode = $auth['code_user'] ?? '';
        $anneeCode = $_SESSION['annee_active_code'] ?? null;

        $stats = $model->getStats($anneeCode, $userCode, $roleCode);

        $this->json([
            'status' => 1,
            'stats' => $stats
        ]);
    }
}
