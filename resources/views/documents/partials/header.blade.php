<div class="doc-header">
  <div class="company-block">
    <h1>{{ $settings['company_name'] ?? 'PURENEXT SARL' }}</h1>
    <p class="tagline">Le meilleur de la nature algérienne</p>
    @if(!empty($settings['company_address']))
      <p>{{ $settings['company_address'] }}</p>
    @endif
    @php
      $legal = array_filter([
        !empty($settings['company_rc'])  ? 'RC: '  . $settings['company_rc']  : null,
        !empty($settings['company_nif']) ? 'NIF: ' . $settings['company_nif'] : null,
        !empty($settings['company_nis']) ? 'NIS: ' . $settings['company_nis'] : null,
        !empty($settings['company_ai'])  ? 'AI: '  . $settings['company_ai']  : null,
      ]);
      $contact = array_filter([
        $settings['company_phone']   ?? null,
        $settings['company_email']   ?? null,
        $settings['company_website'] ?? null,
      ]);
    @endphp
    @if(count($legal))
      <p class="legal">{{ implode('   |   ', $legal) }}</p>
    @endif
    @if(count($contact))
      <p class="contact">{{ implode('  |  ', $contact) }}</p>
    @endif
  </div>
  <div class="doc-type-block">
    <h2>{{ $docType }}</h2>
    <p class="doc-number">{{ $docNumber }}</p>
    <p class="doc-date">Date : {{ $date }}</p>
    @if(!empty($docExtra)) <p class="doc-date">{{ $docExtra }}</p> @endif
  </div>
</div>