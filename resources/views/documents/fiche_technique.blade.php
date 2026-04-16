<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; }
  .header { background: #7c3aed; color: white; padding: 20px; margin-bottom: 24px; }
  .header h1 { margin: 0; font-size: 20px; }
  .header p  { margin: 4px 0 0; font-size: 11px; opacity: .85; }
  .ref { float: right; font-size: 11px; }
  h2 { color: #7c3aed; font-size: 13px; margin: 20px 0 8px; border-bottom: 1px solid #ede9fe; padding-bottom: 4px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #f5f3ff; text-align: left; padding: 8px 10px; border-bottom: 2px solid #7c3aed; }
  td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
  .footer { margin-top: 40px; font-size: 10px; color: #6b7280; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <span class="ref">Réf : {{ $reference }}</span>
  <h1>Fiche Technique Produit</h1>
  <p>Générée le {{ $date }} — PURENEXT SARL</p>
</div>

<h2>Informations générales</h2>
<table>
  <tr><th>Nom du produit</th><td>{{ $data['name'] }}</td></tr>
  <tr><th>Description</th><td>{{ $data['description'] ?? '—' }}</td></tr>
  <tr><th>Durée de conservation</th><td>{{ $data['shelf_life'] }}</td></tr>
  <tr><th>Rendement</th><td>{{ $data['yield'] }}</td></tr>
</table>

<h2>Composition / Ingrédients</h2>
<table>
  <tr>
    <th>Ingrédient</th>
    <th>Quantité</th>
    <th>Unité</th>
  </tr>
  @foreach($data['ingredients'] as $ing)
  <tr>
    <td>{{ $ing['name'] }}</td>
    <td>{{ $ing['quantity'] }}</td>
    <td>{{ $ing['unit'] }}</td>
  </tr>
  @endforeach
</table>

<div class="footer">PURENEXT SARL — Fiche technique — {{ $reference }}</div>
</body>
</html>