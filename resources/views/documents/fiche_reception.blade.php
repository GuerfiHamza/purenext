<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a1a; }

  @page { margin: 15mm 15mm 25mm 15mm; }

  .doc-header { background:#16a34a; color:white; padding:14px 18px;
                display:table; width:100%; }
  .company-block { display:table-cell; vertical-align:top; width:65%; }
  .company-block h1 { font-size:18px; font-weight:bold; margin-bottom:2px; }
  .company-block p  { font-size:9px; opacity:.9; margin-bottom:1px; }
  .company-block .tagline { font-style:italic; opacity:.8; margin-bottom:4px; }
  .doc-type-block { display:table-cell; vertical-align:top; text-align:right; }
  .doc-type-block h2 { font-size:18px; font-weight:bold; letter-spacing:1px; }
  .doc-type-block p  { font-size:10px; opacity:.8; margin-top:3px; }
  .badge { display:inline-block; background:rgba(255,255,255,.2);
           border:1px solid rgba(255,255,255,.4); color:white;
           padding:2px 10px; border-radius:999px; font-size:9px;
           font-weight:bold; margin-top:5px; }

  .section { margin:14px 0; }
  .section-title { font-size:9px; font-weight:bold; text-transform:uppercase;
                   letter-spacing:1px; color:#16a34a; border-bottom:1.5px solid #16a34a;
                   padding-bottom:4px; margin-bottom:10px; }

  .grid-2 { display:table; width:100%; }
  .col { display:table-cell; width:50%; vertical-align:top; padding-right:16px; }
  .col:last-child { padding-right:0; padding-left:16px; }

  .field { margin-bottom:8px; }
  .field-label { font-size:8px; font-weight:bold; text-transform:uppercase;
                 letter-spacing:.5px; color:#9ca3af; margin-bottom:2px; }
  .field-value { font-size:11px; color:#111; font-weight:500; }
  .field-value.mono { font-family:monospace; font-size:10px; }

  .checks { display:table; width:100%; }
  .check-item { display:table-cell; width:25%; padding:10px 8px;
                background:#f9fafb; border-radius:6px; text-align:center; }
  .check-item + .check-item { padding-left:8px; }
  .check-label { font-size:8px; color:#9ca3af; text-transform:uppercase;
                 letter-spacing:.5px; margin-bottom:4px; }
  .check-value { font-size:11px; font-weight:bold; }
  .check-ok  { color:#16a34a; }
  .check-nok { color:#dc2626; }

  .decision-box { padding:12px 16px; border-radius:8px; margin-top:14px;
                  display:table; width:100%; }
  .decision-accepted         { background:#dcfce7; border:1.5px solid #16a34a; }
  .decision-refused          { background:#fee2e2; border:1.5px solid #dc2626; }
  .decision-accepted_reserve { background:#fef9c3; border:1.5px solid #ca8a04; }
  .decision-label { display:table-cell; vertical-align:middle; }
  .decision-label h3 { font-size:14px; font-weight:bold; }
  .accepted-text         { color:#15803d; }
  .refused-text          { color:#dc2626; }
  .accepted_reserve-text { color:#92400e; }
  .decision-meta { display:table-cell; text-align:right; vertical-align:middle; }
  .decision-meta p { font-size:9px; color:#6b7280; }

  .storage-box { background:#eff6ff; border:1.5px solid #bfdbfe; border-radius:8px;
                 padding:12px 16px; margin-top:10px; display:table; width:100%; }
  .storage-cell { display:table-cell; width:50%; vertical-align:middle; }
  .storage-cell .label { font-size:8px; color:#6b7280; text-transform:uppercase;
                         letter-spacing:.5px; margin-bottom:3px; }
  .storage-cell .value { font-size:16px; font-weight:bold; color:#1d4ed8; }

  .sig-zone { display:table; width:100%; margin-top:30px; }
  .sig-box  { display:table-cell; width:50%; padding-right:20px; }
  .sig-label { font-size:9px; font-weight:bold; text-transform:uppercase;
               color:#9ca3af; margin-bottom:6px; }
  .sig-line { border:1px solid #d1d5db; border-radius:3px; height:28px; }
  .sig-hint { font-size:8px; color:#d1d5db; text-align:center; margin-top:3px; }

  .doc-footer { position:fixed; bottom:0; left:0; right:0;
                border-top:1.5px solid #16a34a; padding-top:5px;
                text-align:center; font-size:8px; color:#9ca3af; line-height:1.6; }
  .doc-footer p { margin:0; white-space:nowrap; }
</style>
</head>
<body>

<div class="doc-header">
  <div class="company-block">
    <h1>{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</h1>
    <p class="tagline">Le meilleur de la nature algérienne</p>
    @if(!empty($settings['company_address']))<p>{{ $settings['company_address'] }}</p>@endif
    <span class="badge">FICHE DE RÉCEPTION</span>
  </div>
  <div class="doc-type-block">
    <h2>Fiche Réception</h2>
    <p>{{ $data['receipt_number'] }}</p>
    <p>{{ $data['reception_date'] }}</p>
  </div>
</div>

<div style="padding:0 4px;">

  {{-- Informations générales --}}
  <div class="section">
    <p class="section-title">Informations générales</p>
    <div class="grid-2">
      <div class="col">
        <div class="field">
          <p class="field-label">Matière première</p>
          <p class="field-value">{{ $data['product'] }}</p>
        </div>
        <div class="field">
          <p class="field-label">SKU</p>
          <p class="field-value mono">{{ $data['sku'] }}</p>
        </div>
        <div class="field">
          <p class="field-label">Quantité reçue</p>
          <p class="field-value" style="font-size:15px;color:#16a34a;font-weight:bold;">
            {{ $data['quantity'] }} {{ $data['unit'] }}
          </p>
        </div>
        <div class="field">
          <p class="field-label">Coût unitaire</p>
          <p class="field-value">{{ $data['unit_cost'] }}</p>
        </div>
      </div>
      <div class="col">
        <div class="field">
          <p class="field-label">Fournisseur</p>
          <p class="field-value">{{ $data['supplier_name'] }}</p>
        </div>
        <div class="field">
          <p class="field-label">N° Lot fournisseur</p>
          <p class="field-value mono">{{ $data['supplier_lot'] }}</p>
        </div>
        <div class="field">
          <p class="field-label">DLUO / DLC</p>
          <p class="field-value">{{ $data['dluo_date'] }}</p>
        </div>
        <div class="field">
          <p class="field-label">Réceptionné par</p>
          <p class="field-value">{{ $data['received_by'] }}</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Contrôles --}}
  <div class="section">
    <p class="section-title">Contrôles à la réception</p>
    <div class="checks">
      <div class="check-item">
        <p class="check-label">Visuel</p>
        <p class="check-value {{ $data['visual_check'] === 'conforme' ? 'check-ok' : 'check-nok' }}">
          {{ $data['visual_check'] === 'conforme' ? '✓ Conforme' : '✗ Non conforme' }}
        </p>
      </div>
      <div class="check-item" style="padding-left:8px;">
        <p class="check-label">Odeur</p>
        <p class="check-value {{ $data['smell_check'] === 'conforme' ? 'check-ok' : 'check-nok' }}">
          {{ $data['smell_check'] === 'conforme' ? '✓ Conforme' : '✗ Non conforme' }}
        </p>
      </div>
      <div class="check-item" style="padding-left:8px;">
        <p class="check-label">Température</p>
        <p class="check-value" style="color:#374151;">{{ $data['temperature'] }}</p>
      </div>
      <div class="check-item" style="padding-left:8px;">
        <p class="check-label">Humidité</p>
        <p class="check-value" style="color:#374151;">{{ $data['humidity'] }}</p>
      </div>
    </div>

    @if($data['refractometer_brix'] || $data['refractometer_humidity'])
    <div style="margin-top:10px; background:#f9fafb; border-radius:6px; padding:10px 12px;">
      <p style="font-size:8px;font-weight:bold;text-transform:uppercase;color:#9ca3af;margin-bottom:6px;">
        Réfractomètre (miel)
      </p>
      <div class="grid-2">
        <div class="col">
          <p class="field-label">Brix</p>
          <p class="field-value">{{ $data['refractometer_brix'] ?? '—' }}</p>
        </div>
        <div class="col">
          <p class="field-label">% Humidité</p>
          <p class="field-value">{{ $data['refractometer_humidity'] ?? '—' }}</p>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Décision --}}
  <div class="section">
    <p class="section-title">Décision</p>
    @php
      $decisionLabel = match($data['decision']) {
        'accepted'         => '✓ ACCEPTÉ',
        'refused'          => '✗ REFUSÉ',
        'accepted_reserve' => '⚠ ACCEPTÉ SOUS RÉSERVE',
      };
      $decisionClass = 'decision-' . $data['decision'];
      $textClass = $data['decision'] . '-text';
    @endphp
    <div class="decision-box {{ $decisionClass }}">
      <div class="decision-label">
        <h3 class="{{ $textClass }}">{{ $decisionLabel }}</h3>
      </div>
      <div class="decision-meta">
        <p>Réf : {{ $data['receipt_number'] }}</p>
        <p>{{ $data['reception_date'] }}</p>
      </div>
    </div>
  </div>

  {{-- Stockage --}}
  @if($data['storage_zone'] !== '—' || $data['storage_location'] !== '—')
  <div class="section">
    <p class="section-title">Stockage attribué</p>
    <div class="storage-box">
      <div class="storage-cell">
        <p class="label">Zone</p>
        <p class="value">{{ $data['storage_zone'] }}</p>
      </div>
      <div class="storage-cell">
        <p class="label">Emplacement</p>
        <p class="value">{{ $data['storage_location'] }}</p>
      </div>
    </div>
  </div>
  @endif

  {{-- Notes --}}
  @if(!empty($data['notes']))
  <div class="section">
    <p class="section-title">Notes / Observations</p>
    <div style="background:#f9fafb; border-radius:6px; padding:10px 12px;">
      <p style="font-size:10px; color:#374151;">{{ $data['notes'] }}</p>
    </div>
  </div>
  @endif

  {{-- Signatures --}}
  <div class="sig-zone">
    <div class="sig-box">
      <p class="sig-label">Opérateur réception</p>
      <div class="sig-line"></div>
      <p class="sig-hint">{{ $data['received_by'] }} — Date et signature</p>
    </div>
    <div class="sig-box" style="padding-left:20px; padding-right:0;">
      <p class="sig-label">Responsable qualité</p>
      <div class="sig-line"></div>
      <p class="sig-hint">Visa et cachet</p>
    </div>
  </div>

</div>

<div class="doc-footer">
  <p>{{ $settings['company_name'] ?? 'PURENEXT SARL' }}
    @if(!empty($settings['company_address'])) &nbsp;—&nbsp; {{ $settings['company_address'] }} @endif
  </p>
  <p style="color:#b0b7c3;">{{ $reference }}</p>
</div>

</body>
</html>