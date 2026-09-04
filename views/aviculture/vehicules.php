<?php require_once __DIR__ . '/../../public/inc/header.php'; ?>

<style>
@media print {
  .sidebar, .navbar, .header, #sidebarToggle, .btn,
  .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate, .modal {
    display: none !important;
  }
  .content-wrapper {
    padding: 0 !important;
    background: #FFFFFF !important;
  }
  .card {
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
  }
  .table {
    width: 100% !important;
    border-collapse: collapse !important;
  }
  .table th, .table td {
    border: 1px solid #94A3B8 !important;
    padding: 6px 10px !important;
    font-size: 11px !important;
  }
}
</style>

<div class="content-wrapper" style="padding: 24px; background: #F8FAFC; min-height: 100vh;">
  <!-- EN-TÊTE PAGE ET ACTIONS -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 style="font-size: 24px; font-weight: 900; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="car" style="width: 28px; height: 28px; color: #0F172A;"></i> Flotte de Véhicules de Livraison
      </h1>
      <p style="font-size: 13px; color: #64748B; margin: 4px 0 0 0;">
        Gestion et inventaire des camions, tricycles et véhicules de transport logistique
      </p>
    </div>

    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
      <button type="button" onclick="const pWin = window.open(window.location.href + (window.location.href.indexOf('?') > -1 ? '&' : '?') + 'print=1', '_blank');" class="btn btn-dark" style="font-weight: 800; border-radius: 8px; font-size: 13px; background: #0F172A; border-color: #0F172A; color: #FFFFFF; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; box-shadow: 0 2px 6px rgba(15,23,42,0.2);">
        <i data-lucide="printer" style="width: 16px; height: 16px; color: #FFFFFF;"></i> Imprimer
      </button>
      <a href="<?= RACINE ?>aviculture/livraisons" class="btn btn-light" style="font-weight: 700; border-radius: 8px; font-size: 13px; border: 1px solid #CBD5E1; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;">
        <i data-lucide="arrow-left" style="width: 16px; height: 16px; color: #475569;"></i> Retour aux Expéditions
      </a>
      <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalAddVehicule" style="background: #0F172A; border-color: #0F172A; font-weight: 800; border-radius: 8px; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; box-shadow: 0 4px 12px rgba(15,23,42,0.2);">
        <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Nouveau Véhicule
      </button>
    </div>
  </div>

  <!-- TABLEAU DES VÉHICULES DE LIVRAISON -->
  <div class="card" style="background: #FFFFFF; border-radius: 12px; padding: 24px; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div class="table-responsive">
      <table id="tableVehicules" class="table table-hover align-middle" style="width: 100%; font-size: 13px;">
        <thead style="background: #F8FAFC; color: #475569; font-weight: 800;">
          <tr>
            <th>Code Véhicule</th>
            <th>Immatriculation</th>
            <th>Libellé / Désignation</th>
            <th>Capacité Max (Kg)</th>
            <th>Statut Flotte</th>
            <th>Date Création</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($vehicules)): ?>
            <?php foreach ($vehicules as $v): ?>
              <tr>
                <td style="font-weight: 900; font-family: monospace; color: #0F172A;"><?= htmlspecialchars($v['code_vehicule']) ?></td>
                <td style="font-weight: 800; color: #2563EB; font-family: monospace; font-size: 14px;"><?= htmlspecialchars($v['immatriculation']) ?></td>
                <td style="font-weight: 800; color: #0F172A;"><?= htmlspecialchars($v['libelle_vehicule']) ?></td>
                <td style="font-weight: 800; color: #0369A1;"><?= number_format($v['capacite_max_kg'], 2, ',', ' ') ?> kg</td>
                <td>
                  <?php if ($v['statut'] === 'disponible'): ?>
                    <span style="background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px;">Disponible</span>
                  <?php elseif ($v['statut'] === 'en_livraison'): ?>
                    <span style="background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px;">En Tournée</span>
                  <?php else: ?>
                    <span style="background: #F1F5F9; color: #64748B; border: 1px solid #CBD5E1; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px;"><?= htmlspecialchars($v['statut']) ?></span>
                  <?php endif; ?>
                </td>
                <td style="color: #64748B; font-weight: 600;"><?= date('d/m/Y H:i', strtotime($v['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL NOUVEAU VÉHICULE -->
<div class="modal fade" id="modalAddVehicule" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: #0F172A; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px; padding: 16px 20px;">
        <h5 class="modal-title" style="font-weight: 800; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i data-lucide="car" style="width: 20px; height: 20px; color: #94A3B8;"></i> Ajouter un Véhicule de Transport
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formAddVehicule">
        <input type="hidden" name="csrf_token" value="<?= Validator::generateCsrfToken() ?>">

        <div class="modal-body" style="padding: 20px;">
          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Plaque d'Immatriculation *</label>
            <input type="text" name="immatriculation" required class="form-control" placeholder="ex: 1234-HV-01" style="border-radius: 8px; font-size: 13px;">
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Désignation / Nom du Véhicule *</label>
            <input type="text" name="libelle_vehicule" required class="form-control" placeholder="ex: Camion Frigorifique Isuzu A" style="border-radius: 8px; font-size: 13px;">
          </div>

          <div class="mb-3">
            <label style="font-weight: 800; font-size: 12px; color: #1E293B; margin-bottom: 6px; display: block;">Capacité Maximale de Charge (Kg)</label>
            <input type="number" step="0.1" name="capacite_max_kg" class="form-control" placeholder="ex: 1500.00" style="border-radius: 8px; font-size: 13px;">
          </div>
        </div>

        <div class="modal-footer" style="background: #F8FAFC; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; padding: 12px 20px;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Annuler</button>
          <button type="submit" class="btn btn-dark" style="background: #0F172A; border-color: #0F172A; font-weight: 800; border-radius: 8px; padding: 8px 20px; font-size: 13px;">
            Enregistrer Véhicule
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('#tableVehicules').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            pageLength: 15
        });
    }

    function notifyMsg(message, type = 'success') {
        if (typeof toastr !== 'undefined' && toastr[type]) {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    $('#formAddVehicule').on('submit', function(e) {
        e.preventDefault();
        const baseApi = (typeof RACINE !== 'undefined') ? RACINE : '/ovolias/';
        const $btn = $(this).find('button[type="submit"]');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Enregistrement...');

        $.ajax({
            url: baseApi + 'aviculture/addVehicule',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    notifyMsg(res.message, 'success');
                    $('#modalAddVehicule').modal('hide');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    notifyMsg(res.message || 'Erreur lors de l\'enregistrement', 'error');
                    $btn.prop('disabled', false).html('Enregistrer Véhicule');
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Une erreur est survenue.';
                notifyMsg(msg, 'error');
                $btn.prop('disabled', false).html('Enregistrer Véhicule');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../public/inc/footer.php'; ?>
