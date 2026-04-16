<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a1a; }

  .label-wrap { width:148mm; height:98mm; padding:6mm; position:relative; }

  /* Bande header */
  .label-header { background:#1a3a1a; color:white; padding:5mm 6mm;
                  margin:-6mm -6mm 4mm -6mm; }
  .label-header h1 { font-size:14px; font-weight:bold; }
  .label-header p  { font-size:8px; opacity:.8; margin-top:1px; }

  /* Produit */
  .product-name { font-size:16px; font-weight:bold; color:#1a3a1a;
                  margin-bottom:2mm; line-height:1.2; }
  .product-sku  { font-size:9px; color:#9ca3af; font-family:monospace; margin-bottom:3mm; }

  /* Séparateur */
  .sep { border:none; border-top:1px solid #e5e7eb; margin:3mm 0; }

  /* Grille infos */
  .info-grid { display:table; width:100%; }
  .info-cell { display:table-cell; width:50%; vertical-align:top; }
  .info-label { font-size:7.5px; font-weight:bold; text-transform:uppercase;
                letter-spacing:.5px; color:#9ca3af; margin-bottom:1mm; }
  .info-value { font-size:12px; font-weight:bold; color:#1a3a1a; }
  .info-value.large { font-size:16px; }

  /* Décision badge */
  .decision { display:inline-block; padding:1.5mm 4mm; border-radius:999px;
              font-size:9px; font-weight:bold; margin-top:3mm; }
  .decision-accepted         { background:#dcfce7; color:#15803d; }
  .decision-refused          { background:#fee2e2; color:#dc2626; }
  .decision-accepted_reserve { background:#fef9c3; color:#92400e; }

  /* DLC zone */
  .dluo-box { background:#fff7ed; border:1.5px solid #fed7aa; border-radius:4px;
              padding:2mm 3mm; margin-top:3mm; display:table; width:100%; }
  .dluo-cell { display:table-cell; vertical-align:middle; }
  .dluo-label { font-size:7.5px; color:#92400e; text-transform:uppercase;
                font-weight:bold; letter-spacing:.5px; }
  .dluo-value { font-size:14px; font-weight:bold; color:#c2410c; }

  /* Stockage */
  .storage { background:#eff6ff; border-radius:4px; padding:2mm 3mm;
             margin-top:2mm; display:table; width:100%; }
  .storage-cell { display:table-cell; width:50%; }
  .storage-label { font-size:7px; color:#6b7280; text-transform:uppercase; }
  .storage-value { font-size:13px; font-weight:bold; color:#1d4ed8; }

  /* Footer */
  .label-footer { position:absolute; bottom:6mm; left:6mm; right:6mm;
                  border-top:1px solid #e5e7eb; padding-top:2mm; }
  .label-footer p { font-size:7px; color:#9ca3af; text-align:center; }
</style>
</head>
<body>
<div class="label-wrap">

  <div class="label-header">
    <h1>{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</h1>
    <p>Étiquette Stock Matière Première</p>
  </div>

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

  <div class="info-grid" style="margin-top:3mm;">
    <div class="info-cell">
      <p class="info-label">Fournisseur</p>
      <p class="info-value" style="font-size:11px;">{{ $data['supplier_name'] }}</p>
    </div>
    <div class="info-cell">
      <p class="info-label">Lot fournisseur</p>
      <p class="info-value" style="font-family:monospace;font-size:11px;">{{ $data['supplier_lot'] }}</p>
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
      <p style="font-size:9px;font-family:monospace;color:#92400e;font-weight:bold;">
        {{ $data['receipt_number'] }}
      </p>
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

  <div class="label-footer">
    <p>{{ $settings['company_name'] ?? 'PURENEXT SARL' }} &nbsp;·&nbsp; {{ $reference }} &nbsp;·&nbsp; {{ $date }}</p>
  </div>

</div>
</body>
</html>