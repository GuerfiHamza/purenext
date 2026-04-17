<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#1a1a1a; padding:0; }
  @page {
    margin-bottom: 110px;
  }
  /* ── Header ── */
  .doc-header { background:#16a34a; color:white; padding:16px 20px; display:table; width:100%; }
  .company-block { display:table-cell; vertical-align:top; width:65%; }
  .company-block h1 { font-size:20px; font-weight:bold; margin-bottom:3px; }
  .company-block .tagline { font-size:9px; font-style:italic; opacity:.85; margin-bottom:6px; }
  .company-block p { font-size:9px; opacity:.9; margin-bottom:2px; }
  .company-block .legal { font-size:8px; opacity:.75; margin-top:4px; }
  .company-block .contact { font-size:8px; opacity:.8; }
  .doc-type-block { display:table-cell; vertical-align:top; text-align:right; }
  .doc-type-block h2 { font-size:22px; font-weight:bold; letter-spacing:2px; text-transform:uppercase; }
  .doc-type-block .doc-number { font-size:12px; opacity:.9; margin-top:4px; }
  .doc-type-block .doc-date { font-size:10px; opacity:.75; margin-top:2px; }

  /* ── Parties ── */
  .parties { display:table; width:100%; margin:16px 0; }
  .party { display:table-cell; width:33%; padding:12px 14px; background:#f9fafb; border-radius:4px; vertical-align:top; }
  .party + .party { padding-left:10px; }
  .party-label { font-size:8px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; color:#9ca3af; margin-bottom:6px; }
  .party-name  { font-size:13px; font-weight:bold; color:#111; margin-bottom:4px; }
  .party-info  { font-size:9px; color:#6b7280; line-height:1.7; }

  /* ── Table ── */
  table { width:100%; border-collapse:collapse; margin:10px 0; }
  thead tr { background:#16a34a; }
  th { padding:8px 10px; font-size:9px; font-weight:bold; text-transform:uppercase;
       letter-spacing:.5px; color:white; text-align:left; }
  th.r, td.r { text-align:right; }
  td { padding:9px 10px; font-size:10px; color:#374151; border-bottom:1px solid #f3f4f6; }
  tr:nth-child(even) td { background:#f9fafb; }

  /* ── Totaux ── */
  .totals-wrap { text-align:right; margin-top:8px; }
  .totals-inner { display:inline-block; width:240px; }
  .total-row { display:table; width:100%; padding:4px 0; font-size:11px; color:#6b7280; }
  .total-row span { display:table-cell; }
  .total-row span:last-child { text-align:right; }
  .total-final { display:table; width:100%; background:#16a34a; border-radius:4px;
                 padding:8px 10px; margin-top:6px; }
  .total-final span { display:table-cell; font-size:13px; font-weight:bold; color:white; }
  .total-final span:last-child { text-align:right; }

  /* ── Notes / Paiement ── */
  .payment-note { margin-top:16px; font-size:9px; font-style:italic; color:#6b7280; }
  .notes-box { margin-top:10px; background:#f9fafb; border-radius:4px; padding:10px 12px; }
  .notes-box .label { font-size:8px; font-weight:bold; text-transform:uppercase;
                      letter-spacing:1px; color:#9ca3af; margin-bottom:3px; }
  .notes-box p { font-size:9px; color:#6b7280; }

  /* ── Footer ── */
  .doc-footer {
    position: fixed;
    bottom: 20px;
    left: 0;
    right: 0;
    border-top: 1.5px solid #92400e; /* change la couleur selon le doc */
    padding-top: 6px;
    text-align: center;
    font-size: 8px;
    color: #9ca3af;
  }
  .footer-note { font-style:italic; margin-bottom:3px; color:#6b7280; }

  /* ── Badge ── */
  .badge-facture { display:inline-block; background:rgba(255,255,255,.2);
                   border:1px solid rgba(255,255,255,.4); color:white;
                   padding:2px 10px; border-radius:999px; font-size:9px;
                   font-weight:bold; margin-top:6px; }
</style>
</head>
<body>

{{-- Header --}}
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
      $contact = array_filter([
        $settings['company_phone']   ?? null,
        $settings['company_email']   ?? null,
        $settings['company_website'] ?? null,
      ]);
    @endphp
    @if(count($legal))<p class="legal">{{ implode('   |   ', $legal) }}</p>@endif
    @if(count($contact))<p class="contact">{{ implode('  |  ', $contact) }}</p>@endif
    <span class="badge-facture">FACTURE</span>
  </div>
  <div class="doc-type-block">
    <h2>Facture</h2>
    <p class="doc-number">{{ $data['invoice_number'] }}</p>
    <p class="doc-date">Émise le {{ $date }}</p>
    @if($data['delivery_date'] !== '—')
      <p class="doc-date">Livraison : {{ $data['delivery_date'] }}</p>
    @endif
  </div>
</div>

{{-- Parties --}}
<div style="padding:0 20px;">
  <div class="parties">
    <div class="party">
      <p class="party-label">Émetteur</p>
      <p class="party-name">{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</p>
      <p class="party-info">
        Commercial : {{ $data['commercial'] }}<br>
        @if(!empty($settings['company_phone'])){{ $settings['company_phone'] }}<br>@endif
        @if(!empty($settings['company_email'])){{ $settings['company_email'] }}@endif
    @if(!empty($settings['company_address']))<p>{{ $settings['company_address'] }}</p>@endif
    @if(!empty($settings['company_rc']))RC : {{ $settings['company_rc'] }}<br>@endif
    @if(!empty($settings['company_nif']))NIF : {{ $settings['company_nif'] }}<br>@endif
    @if(!empty($settings['company_nis']))NIS : {{ $settings['company_nis'] }}<br>@endif
    @if(!empty($settings['company_ai']))AI : {{ $settings['company_ai'] }}@endif
      </p>
    </div>
    <div class="party" style="padding-left:14px;">
      <p class="party-label">Facturé à</p>
        <p class="party-name">{{ $data['client_name'] }}</p>
  <p class="party-info">
    @if($data['client_phone'] !== '—')Tél : {{ $data['client_phone'] }}<br>@endif
    @if($data['client_email'] !== '—'){{ $data['client_email'] }}<br>@endif
    @if($data['client_address'] !== '—'){{ $data['client_address'] }}<br>@endif
    @if(!empty($data['client_rc']))RC : {{ $data['client_rc'] }}<br>@endif
    @if(!empty($data['client_nif']))NIF : {{ $data['client_nif'] }}<br>@endif
    @if(!empty($data['client_nis']))NIS : {{ $data['client_nis'] }}<br>@endif
    @if(!empty($data['client_ai']))AI : {{ $data['client_ai'] }}@endif
  </p>
    </div>
    <div class="party" style="padding-left:14px;">
      <p class="party-label">Commande</p>
      <p class="party-name">{{ $data['order_number'] }}</p>
      <p class="party-info">
        Date : {{ $data['order_date'] }}<br>
        Réf : {{ $data['invoice_number'] }}
      </p>
    </div>
  </div>

  {{-- Tableau produits --}}
  <table>
    <thead>
      <tr>
        <th style="width:30px;">#</th>
        <th style="width:280px;">Produit</th>
        <th>Lot</th>
        <th>Format</th>
        <th class="r">Qté</th>
        <th class="r">Prix unit. (DA)</th>
        <th class="r">Total (DA)</th>
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
        <td class="r">{{ $item['unit_price'] }}</td>
        <td class="r" style="font-weight:bold;">{{ $item['total_price'] }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Totaux --}}
  <div class="totals-wrap">
    <div class="totals-inner">
      <div class="total-row">
        <span>Sous-total HT</span>
        <span>{{ $data['subtotal'] }} DA</span>
      </div>
      @if($data['tva_enabled'])
      <div class="total-row">
        <span>TVA ({{ $data['tva_rate'] }}%)</span>
        <span>{{ $data['tva_amount'] }} DA</span>
      </div>
      @endif
      <div class="total-final">
        <span>Total TTC</span>
        <span>{{ $data['total_ttc'] }} DA</span>
      </div>
    </div>
  </div>

  @if(!empty($data['payment_terms']))
    <p class="payment-note">{{ $data['payment_terms'] }}</p>
  @endif

  @if(!empty($data['notes']))
    <div class="notes-box">
      <p class="label">Notes</p>
      <p>{{ $data['notes'] }}</p>
    </div>
  @endif

  {{-- Footer --}}
  
</div>
@include('documents.partials.footer')
</body>
</html>