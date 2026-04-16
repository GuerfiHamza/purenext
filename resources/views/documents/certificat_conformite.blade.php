<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; }
  .header { background: #1d4ed8; color: white; padding: 20px; margin-bottom: 24px; }
  .header h1 { margin: 0; font-size: 20px; }
  .header p  { margin: 4px 0 0; font-size: 11px; opacity: .85; }
  .ref { float: right; font-size: 11px; }
  .seal { text-align: center; margin: 30px 0; }
  .seal-box { display: inline-block; border: 3px solid #1d4ed8;
              border-radius: 8px; padding: 16px 32px; }
  .seal-box h2 { color: #1d4ed8; margin: 0 0 4px; }
  table { width: 100%; border-collapse: collapse; margin-top: 16px; }
  th { background: #eff6ff; text-align: left; padding: 8px 10px; border-bottom: 2px solid #1d4ed8; }
  td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
  .footer { margin-top: 40px; font-size: 10px; color: #6b7280; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <span class="ref">Réf : {{ $reference }}</span>
  <h1>Certificat de Conformité</h1>
  <p>Émis le {{ $date }} — PURENEXT SARL</p>
</div>

<div class="seal">
  <div class="seal-box">
    <h2>✓ CONFORME</h2>
    <p style="margin:0;color:#374151;">Lot : {{ $data['lot_number'] }}</p>
  </div>
</div>

<table>
<tr><th>Lot / Batch</th><td>{{ $data['lot_number'] }} / {{ $data['batch_number'] }}</td></tr>
<tr><th>Produit</th><td>{{ $data['product'] }}</td></tr>
<tr><th>Format</th><td>{{ $data['packaging'] }}</td></tr>
<tr><th>Quantité produite</th><td>{{ $data['quantity'] }}</td></tr>
<tr><th>Date de production</th><td>{{ $data['production_date'] }}</td></tr>
<tr><th>DLC / Expiration</th><td>{{ $data['expiry_date'] }}</td></tr>
<tr><th>Durée de conservation</th><td>{{ $data['shelf_life'] }}</td></tr>
</table>

<div class="footer">PURENEXT SARL — Certificat de conformité — {{ $reference }}</div>
</body>
</html>