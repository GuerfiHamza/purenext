<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; }
  .header { background: #16a34a; color: white; padding: 20px; margin-bottom: 24px; }
  .header h1 { margin: 0; font-size: 20px; }
  .header p  { margin: 4px 0 0; font-size: 11px; opacity: .85; }
  .ref { float: right; font-size: 11px; }
  table { width: 100%; border-collapse: collapse; margin-top: 16px; }
  th { background: #f0fdf4; text-align: left; padding: 8px 10px; border-bottom: 2px solid #16a34a; }
  td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
  .badge { display: inline-block; padding: 2px 10px; border-radius: 999px;
           font-size: 10px; font-weight: bold; background: #dcfce7; color: #15803d; }
  .footer { margin-top: 40px; font-size: 10px; color: #6b7280; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <span class="ref">Réf : {{ $reference }}</span>
  <h1>Rapport de Production</h1>
  <p>Généré le {{ $date }} — PURENEXT SARL</p>
</div>

<table>
  <tr><th>Numéro de lot</th><td>{{ $data['lot_number'] }}</td></tr>
  <tr><th>Recette</th><td>{{ $data['recipe'] }}</td></tr>
  <tr><th>Quantité produite</th><td>{{ $data['quantity'] }} {{ $data['unit'] }}</td></tr>
  <tr><th>Début</th><td>{{ $data['started_at'] }}</td></tr>
  <tr><th>Fin</th><td>{{ $data['finished_at'] ?? '—' }}</td></tr>
  <tr><th>Opérateur</th><td>{{ $data['operator'] }}</td></tr>
  <tr><th>Statut</th><td><span class="badge">{{ $data['status'] }}</span></td></tr>
  @if($data['notes'])
  <tr><th>Notes</th><td>{{ $data['notes'] }}</td></tr>
  @endif
</table>

<div class="footer">PURENEXT SARL — Document généré automatiquement — {{ $reference }}</div>
</body>
</html>