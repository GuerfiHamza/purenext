<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a1a; }

  .label-wrap { width:148mm; height:98mm; padding:0; position:relative; overflow:hidden; }

  /* Header */
  .label-header { background:#1a3a1a; color:white; padding:4mm 5mm; }
  .label-header h1 { font-size:13px; font-weight:bold; }
  .label-header p  { font-size:7.5px; opacity:.8; margin-top:1px; }

  /* Body padding */
  .label-body { padding:3mm 5mm; }

  /* Produit */
  .product-name { font-size:14px; font-weight:bold; color:#1a3a1a; margin-bottom:1mm; line-height:1.2; }
  .product-sku  { font-size:8px; color:#9ca3af; font-family:monospace; margin-bottom:1.5mm; }

  /* Séparateur */
  .sep { border:none; border-top:1px solid #e5e7eb; margin:1.5mm 0; }

  /* Grille infos */
  .info-grid { display:table; width:100%; }
  .info-cell { display:table-cell; width:50%; vertical-align:top; }
  .info-label { font-size:7px; font-weight:bold; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; margin-bottom:.5mm; }
  .info-value { font-size:11px; font-weight:bold; color:#1a3a1a; }
  .info-value.large { font-size:14px; }

  /* Décision badge */
  .decision { display:inline-block; padding:1mm 3mm; border-radius:999px; font-size:8px; font-weight:bold; margin-top:1.5mm; }
  .decision-accepted         { background:#dcfce7; color:#15803d; }
  .decision-refused          { background:#fee2e2; color:#dc2626; }
  .decision-accepted_reserve { background:#fef9c3; color:#92400e; }

  /* DLUO */
  .dluo-box { background:#fff7ed; border:1.5px solid #fed7aa; border-radius:4px; padding:1.5mm 3mm; margin-top:1.5mm; display:table; width:100%; }
  .dluo-cell { display:table-cell; vertical-align:middle; width:50%; }
  .dluo-label { font-size:7px; color:#92400e; text-transform:uppercase; font-weight:bold; letter-spacing:.5px; }
  .dluo-value { font-size:12px; font-weight:bold; color:#c2410c; }
  .dluo-ref   { font-size:7px; font-family:monospace; color:#92400e; font-weight:bold; word-break:break-all; }

  /* Stockage */
  .storage { background:#eff6ff; border-radius:4px; padding:1.5mm 3mm; margin-top:1.5mm; display:table; width:100%; }
  .storage-cell { display:table-cell; width:50%; }
  .storage-label { font-size:7px; color:#6b7280; text-transform:uppercase; }
  .storage-value { font-size:12px; font-weight:bold; color:#1d4ed8; }

  /* Footer */
  .label-footer { position:absolute; bottom:3mm; left:5mm; right:5mm; border-top:1px solid #e5e7eb; padding-top:1.5mm; }
  .label-footer p { font-size:6.5px; color:#9ca3af; text-align:center; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
</style>
</head>
<body>
<div class="label-wrap">

  <div class="label-header">
    <h1>{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</h1>
    <p>Étiquette Stock Matière Première</p>
  </div>

  <div class="label-body">

    <p class="product-name">{{ $data['product'] }}</p>
    @if($data['sku'] !== '—')
      <p class="product-sku">{{ $data['sku'] }}</p>
    @endif

    <hr class="sep">

    <div class="info-grid">
      <div class="info-cell">
        <p class="info-label">Quantité</p>
        <p class="info-value large">{{ $data['quantity'] }} {{ $data['unit'] }}</p>
      </div>
      <div class="info-cell">
        <p class="info-label">Date réception</p>
        <p class="info-value">{{ $data['reception_date'] }}</p>
      </div>
    </div>

    <div class="info-grid" style="margin-top:2mm;">
      <div class="info-cell">
        <p class="info-label">Fournisseur</p>
        <p class="info-value" style="font-size:10px;">{{ $data['supplier_name'] }}</p>
      </div>
      <div class="info-cell">
        <p class="info-label">Lot fournisseur</p>
        <p class="info-value" style="font-family:monospace;font-size:10px;">{{ $data['supplier_lot'] }}</p>
      </div>
    </div>

    @if($data['dluo_date'] !== '—')
    <div class="dluo-box">
      <div class="dluo-cell">
        <p class="dluo-label">DLUO / DLC</p>
        <p class="dluo-value">{{ $data['dluo_date'] }}</p>
      </div>
      <div class="dluo-cell" style="text-align:right;">
        <p class="dluo-label">Réf.</p>
        <p class="dluo-ref">{{ $data['receipt_number'] }}</p>
      </div>
    </div>
    @endif

    @if($data['storage_zone'] !== '—' || $data['storage_location'] !== '—')
    <div class="storage">
      <div class="storage-cell">
        <p class="storage-label">Zone</p>
        <p class="storage-value">{{ $data['storage_zone'] }}</p>
      </div>
      <div class="storage-cell">
        <p class="storage-label">Emplacement</p>
        <p class="storage-value">{{ $data['storage_location'] }}</p>
      </div>
    </div>
    @endif

    @php
      $decisionLabel = match($data['decision']) {
        'accepted'         => '✓ ACCEPTÉ',
        'refused'          => '✗ REFUSÉ',
        'accepted_reserve' => '⚠ SOUS RÉSERVE',
      };
    @endphp
    <span class="decision decision-{{ $data['decision'] }}">{{ $decisionLabel }}</span>

  </div>

  <div class="label-footer">
    <p>{{ $settings['company_name'] ?? 'PURENEXT SARL' }} · {{ Str::limit($reference, 20) }} · {{ $date }}</p>
  </div>

</div>
</body>
</html>