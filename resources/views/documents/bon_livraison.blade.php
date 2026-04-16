<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a1a; padding:0; }
  .doc-header { background:#1d4ed8; color:white; padding:16px 20px; display:table; width:100%; }
  .company-block { display:table-cell; vertical-align:top; width:65%; }
  .company-block h1 { font-size:20px; font-weight:bold; margin-bottom:3px; }
  .company-block p  { font-size:9px; opacity:.9; margin-bottom:2px; }
  .company-block .tagline { font-style:italic; opacity:.8; margin-bottom:5px; }
  .company-block .legal   { font-size:8px; opacity:.75; margin-top:3px; }
  .doc-type-block { display:table-cell; vertical-align:top; text-align:right; }
  .doc-type-block h2 { font-size:22px; font-weight:bold; letter-spacing:2px; text-transform:uppercase; }
  .doc-type-block p  { font-size:10px; opacity:.8; margin-top:3px; }
  .badge { display:inline-block; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.4);
           color:white; padding:2px 10px; border-radius:999px; font-size:9px; font-weight:bold; margin-top:5px; }
  .parties { display:table; width:100%; margin:14px 0; }
  .party { display:table-cell; width:33%; padding:12px 14px; background:#f9fafb; vertical-align:top; }
  .party-label { font-size:8px; font-weight:bold; text-transform:uppercase;
                 letter-spacing:1px; color:#9ca3af; margin-bottom:5px; }
  .party-name  { font-size:13px; font-weight:bold; color:#111; margin-bottom:4px; }
  .party-info  { font-size:9px; color:#6b7280; line-height:1.7; }
  table { width:100%; border-collapse:collapse; margin:10px 0; }
  thead tr { background:#1d4ed8; }
  th { padding:8px 10px; font-size:9px; font-weight:bold; text-transform:uppercase; color:white; text-align:left; }
  th.r, td.r { text-align:right; }
  td { padding:9px 10px; font-size:10px; border-bottom:1px solid #f3f4f6; }
  tr:nth-child(even) td { background:#f9fafb; }
  .sig-zone { display:table; width:100%; margin-top:40px; }
  .sig-box { display:table-cell; width:50%; padding-right:20px; }
  .sig-label { font-size:9px; font-weight:bold; text-transform:uppercase;
               letter-spacing:1px; color:#9ca3af; margin-bottom:6px; }
  .sig-line { border:1px solid #d1d5db; border-radius:3px; height:28px; margin-top:4px; }
  .sig-hint { font-size:8px; color:#d1d5db; text-align:center; margin-top:3px; }
  .doc-footer { margin-top:20px; border-top:1.5px solid #1d4ed8; padding-top:8px;
                text-align:center; font-size:8px; color:#9ca3af; }
  .footer-note { font-style:italic; margin-bottom:3px; color:#6b7280; }
  .notes-box { margin-top:10px; background:#f0f9ff; border-radius:4px; padding:10px 12px; }
  .notes-box .label { font-size:8px; font-weight:bold; text-transform:uppercase; color:#9ca3af; margin-bottom:3px; }
</style>
</head>
<body>

<div class="doc-header">
  <div class="company-block">
    <h1>{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</h1>
    <p class="tagline">Le meilleur de la nature algérienne</p>
    @if(!empty($settings['company_address']))<p>{{ $settings['company_address'] }}</p>@endif
    @php
      $legal = array_filter([
        !empty($settings['company_rc'])  ? 'RC: '.$settings['company_rc']   : null,
        !empty($settings['company_nif']) ? 'NIF: '.$settings['company_nif'] : null,
        !empty($settings['company_nis']) ? 'NIS: '.$settings['company_nis'] : null,
      ]);
    @endphp
    @if(count($legal))<p class="legal">{{ implode('  |  ', $legal) }}</p>@endif
    <span class="badge">BON DE LIVRAISON</span>
  </div>
  <div class="doc-type-block">
    <h2>Bon de Livraison</h2>
    <p>{{ $data['bl_number'] }}</p>
    <p>Émis le {{ $date }}</p>
  </div>
</div>

<div style="padding:0 20px;">
  <div class="parties">
    <div class="party">
      <p class="party-label">Expéditeur</p>
      <p class="party-name">{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</p>
      <p class="party-info">
        Commercial : {{ $data['commercial'] }}<br>
        @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}@endif
      </p>
    </div>
    <div class="party" style="padding-left:14px;">
      <p class="party-label">Destinataire</p>
      <p class="party-name">{{ $data['client_name'] }}</p>
      <p class="party-info">
        @if($data['client_phone'] !== '—')Tél : {{ $data['client_phone'] }}<br>@endif
        @if($data['client_address'] !== '—'){{ $data['client_address'] }}@endif
      </p>
    </div>
    <div class="party" style="padding-left:14px;">
      <p class="party-label">Réf. Commande</p>
      <p class="party-name">{{ $data['order_number'] }}</p>
      <p class="party-info">
        Commande : {{ $data['order_date'] }}<br>
        Livraison prévue : {{ $data['delivery_date'] }}
      </p>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:30px;">#</th>
        <th>Désignation</th>
        <th>N° Lot</th>
        <th>Format</th>
        <th class="r">Quantité</th>
        <th style="width:80px;">Reçu ✓</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data['items'] as $i => $item)
      <tr>
        <td style="color:#9ca3af;">{{ $i + 1 }}</td>
        <td style="font-weight:600;">{{ $item['product'] }}</td>
        <td style="font-size:9px;font-family:monospace;color:#9ca3af;">{{ $item['lot'] }}</td>
        <td>{{ $item['format'] }}</td>
        <td class="r">{{ $item['quantity'] }}</td>
        <td style="color:#e5e7eb;">____________</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @if(!empty($data['notes']))
    <div class="notes-box">
      <p class="label">Notes</p>
      <p style="font-size:9px;color:#6b7280;">{{ $data['notes'] }}</p>
    </div>
  @endif

  <div class="sig-zone">
    <div class="sig-box">
      <p class="sig-label">Signature livreur</p>
      <div class="sig-line"></div>
      <p class="sig-hint">{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</p>
    </div>
    <div class="sig-box">
      <p class="sig-label">Signature client — Lu et approuvé, reçu conforme</p>
      <div class="sig-line"></div>
      <p class="sig-hint">Date, cachet et signature</p>
    </div>
  </div>

  <div class="doc-footer">
    @if(!empty($data['delivery_notes']))
      <p class="footer-note">{{ $data['delivery_notes'] }}</p>
    @endif
    <p>
      {{ $settings['company_name'] ?? 'PURENEXT SARL' }}
      @if(!empty($settings['company_address'])) — {{ $settings['company_address'] }} @endif
      @if(!empty($settings['company_phone'])) — {{ $settings['company_phone'] }} @endif
    </p>
    <p style="margin-top:3px;">{{ $reference }}</p>
  </div>
</div>
</body>
</html>