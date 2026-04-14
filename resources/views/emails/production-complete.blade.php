@component('mail::message')
# ✅ Production Terminée — PURENEXT

Bonjour,

Une production vient d'être clôturée avec succès.

@component('mail::panel')
**Numéro de batch** : {{ $batchNumber }}

**Recette** : {{ $recipeName }}

**Packets estimés** : {{ number_format($packetsEstimated, 0, ',', ' ') }}

**Packets réels** : {{ number_format($packetsActual, 0, ',', ' ') }}

**Opérateur** : {{ $operatorName }}

**Date** : {{ now()->format('d/m/Y à H:i') }}
@endcomponent

@php
$diff = $packetsActual - $packetsEstimated;
$pct = $packetsEstimated > 0 ? round(abs($diff) / $packetsEstimated * 100, 1) : 0;
@endphp

@if($diff < 0)
> ⚠️ Rendement inférieur à l'estimation de {{ $pct }}%
@elseif($diff > 0)
> ✅ Rendement supérieur à l'estimation de {{ $pct }}%
@else
> ✅ Rendement conforme à l'estimation
@endif

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:3000') . '/dashboard/productions', 'color' => 'green'])
Voir les productions
@endcomponent

Cordialement,
**PURENEXT SARL**
contact@purenext.dz

@component('mail::subcopy')
Cet email a été envoyé automatiquement par le système de gestion PURENEXT.
@endcomponent
@endcomponent