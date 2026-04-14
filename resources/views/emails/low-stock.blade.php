@component('mail::message')
# ⚠️ Alerte Stock Bas — PURENEXT

Bonjour,

Un stock critique a été détecté dans votre système de gestion PURENEXT.

@component('mail::panel')
**{{ $type === 'raw_material' ? 'Matière Première' : 'Produit Fini' }}** : {{ $itemName }}

**Stock actuel** : {{ $currentStock }} {{ $unit }}

**Seuil minimum** : {{ $minStock }} {{ $unit }}
@endcomponent

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:3000') . '/dashboard/matieres-premieres', 'color' => 'green'])
Voir le stock
@endcomponent

Veuillez procéder au réapprovisionnement dès que possible.

Cordialement,
**PURENEXT SARL**
contact@purenext.dz

@component('mail::subcopy')
Cet email a été envoyé automatiquement par le système de gestion PURENEXT. Ne pas répondre à cet email.
@endcomponent
@endcomponent