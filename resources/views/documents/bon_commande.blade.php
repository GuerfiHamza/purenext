<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
      @page {
    margin-bottom: 30px;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a1a; padding:0; }
  .doc-header { background:#92400e; color:white; padding:16px 20px; display:table; width:100%; }
  .company-block { display:table-cell; vertical-align:top; width:65%; }
  .company-block h1 { font-size:20px; font-weight:bold; margin-bottom:3px; }
  .company-block p  { font-size:9px; opacity:.9; margin-bottom:2px; }
  .company-block .tagline { font-style:italic; opacity:.8; margin-bottom:5px; }
  .company-block .legal   { font-size:8px; opacity:.75; margin-top:3px; }
  .doc-type-block { display:table-cell; vertical-align:top; text-align:right; }
  .doc-type-block h2 { font-size:22px; font-weight:bold; letter-spacing:2px; }
  .doc-type-block p  { font-size:10px; opacity:.8; margin-top:3px; }
  .badge { display:inline-block; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.4);
           color:white; padding:2px 10px; border-radius:999px; font-size:9px; font-weight:bold; margin-top:5px; }
  .parties { display:table; width:100%; margin:14px 0; }
  .party { display:table-cell; width:50%; padding:12px 14px; background:#fffbeb; vertical-align:top; }
  .party + .party { padding-left:14px; }
  .party-label { font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#92400e; margin-bottom:5px; }
  .party-name  { font-size:13px; font-weight:bold; color:#111; margin-bottom:4px; }
  .party-info  { font-size:9px; color:#6b7280; line-height:1.7; }
  table { width:100%; border-collapse:collapse; margin:10px 0; }
  thead tr { background:#92400e; }
  th { padding:8px 10px; font-size:9px; font-weight:bold; text-transform:uppercase; color:white; text-align:left; }
  th.r, td.r { text-align:right; }
  td { padding:9px 10px; font-size:10px; border-bottom:1px solid #f3f4f6; }
  tr:nth-child(even) td { background:#fffbeb; }
  .total-final { display:table; width:240px; margin-left:auto; background:#92400e;
                 border-radius:4px; padding:8px 10px; margin-top:8px; }
  .total-final span { display:table-cell; font-size:13px; font-weight:bold; color:white; }
  .total-final span:last-child { text-align:right; }
  .sig-zone { display:table; width:100%; margin-top:40px; }
  .sig-box { display:table-cell; width:50%; padding-right:20px; }
  .sig-label { font-size:9px; font-weight:bold; text-transform:uppercase; color:#9ca3af; margin-bottom:6px; }
  .sig-line { border:1px solid #d1d5db; border-radius:3px; height:28px; margin-top:4px; }
  .sig-hint { font-size:8px; color:#d1d5db; text-align:center; margin-top:3px; }
 .doc-footer {
    position: fixed;
    bottom: -20px;
    left: 0;
    right: 0;
    border-top: 1.5px solid #92400e; /* change la couleur selon le doc */
    padding-top: 6px;
    text-align: center;
    font-size: 8px;
    color: #9ca3af;
  }
  .po-notes { margin-top:12px; background:#fffbeb; border-radius:4px; padding:10px 12px; }
  .po-notes .label { font-size:8px; font-weight:bold; text-transform:uppercase; color:#92400e; margin-bottom:3px; }
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
        !empty($settings['company_ai'])  ? 'AI: '.$settings['company_ai']   : null,
      ]);
    @endphp
    @if(count($legal))<p class="legal">{{ implode('   |   ', $legal) }}</p>@endif
    <span class="badge">BON DE COMMANDE</span>
  </div>
  <div class="doc-type-block">
    <h2>Bon de Commande</h2>
    <p>{{ $data['po_number'] }}</p>
    <p>Émis le {{ $date }}</p>
    @if($data['delivery_date'] !== '—')
      <p>Livraison souhaitée : {{ $data['delivery_date'] }}</p>
    @endif
  </div>
</div>

<div style="padding:0 20px;">
  <div class="parties">
    <div class="party">
      <p class="party-label">Acheteur</p>
      <p class="party-name">{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</p>
      <p class="party-info">
        @if(!empty($settings['company_address'])){{ $settings['company_address'] }}<br>@endif
        @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
        @if(!empty($settings['company_email'])){{ $settings['company_email'] }}@endif
      </p>
    </div>
    <div class="party">
      <p class="party-label">Fournisseur</p>
      <p class="party-name">{{ $data['supplier_name'] }}</p>
      <p class="party-info">
        @if($data['contact_name']    !== '—')Contact : {{ $data['contact_name'] }}<br>@endif
        @if($data['supplier_phone']  !== '—')Tél : {{ $data['supplier_phone'] }}<br>@endif
        @if($data['supplier_email']  !== '—'){{ $data['supplier_email'] }}<br>@endif
        @if($data['supplier_country'] !== '—'){{ $data['supplier_country'] }}@endif
      </p>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:25px;">#</th>
        <th>Désignation</th>
        <th>SKU</th>
        <th class="r">Stock actuel</th>
        <th class="r">Qté à commander</th>
        <th>Unité</th>
        @if($data['has_prices'])
          <th class="r">Prix unit. (DA)</th>
          <th class="r">Total (DA)</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @foreach($data['items'] as $i => $item)
      <tr>
        <td style="color:#9ca3af;">{{ $i + 1 }}</td>
        <td style="font-weight:600;">{{ $item['name'] }}</td>
        <td style="font-size:9px;font-family:monospace;color:#9ca3af;">{{ $item['sku'] ?? '—' }}</td>
        <td class="r" style="color:#6b7280;">{{ $item['current_stock'] ?? '—' }}</td>
        <td class="r" style="font-weight:bold;">{{ $item['quantity_needed'] }}</td>
        <td>{{ $item['unit'] }}</td>
        @if($data['has_prices'])
          <td class="r">{{ $item['unit_price'] ? number_format($item['unit_price'], 2, ',', ' ') : '—' }}</td>
          <td class="r" style="font-weight:bold;">
            {{ $item['unit_price'] ? number_format($item['quantity_needed'] * $item['unit_price'], 2, ',', ' ') : '—' }}
          </td>
        @endif
      </tr>
      @endforeach
    </tbody>
  </table>

  @if($data['has_prices'])
    <div class="total-final">
      <span>Total estimé</span>
      <span>{{ $data['total'] }} DA</span>
    </div>
  @endif

  @if(!empty($data['po_notes']))
    <div class="po-notes">
      <p class="label">Instructions</p>
      <p style="font-size:9px;color:#6b7280;font-style:italic;">{{ $data['po_notes'] }}</p>
    </div>
  @endif

  @if(!empty($data['notes']))
    <div class="po-notes" style="margin-top:6px;">
      <p class="label">Notes commande</p>
      <p style="font-size:9px;color:#6b7280;">{{ $data['notes'] }}</p>
    </div>
  @endif

  <div class="sig-zone">
    <div class="sig-box">
      <p class="sig-label">Validé par — {{ $settings['company_name'] ?? 'PURENEXT SARL' }}</p>
      <div class="sig-line"></div>
      <p class="sig-hint">Nom, date et signature</p>
    </div>
    <div class="sig-box">
      <p class="sig-label">Accusé de réception fournisseur</p>
      <div class="sig-line"></div>
      <p class="sig-hint">Cachet, date et signature</p>
    </div>
  </div>

  <div class="doc-footer">
    <p>
      {{ $settings['company_name'] ?? 'PURENEXT SARL' }}
      @if(!empty($settings['company_address'])) — {{ $settings['company_address'] }} @endif
      @if(!empty($settings['company_phone'])) — {{ $settings['company_phone'] }} @endif
    </p>
    @if(!empty($settings['company_rib']) && !empty($settings['company_bank']))
      <p>RIB : {{ $settings['company_rib'] }} — {{ $settings['company_bank'] }}</p>
    @endif
    <p style="margin-top:3px;">{{ $reference }}</p>
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
</body>
</html>