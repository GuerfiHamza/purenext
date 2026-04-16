<div class="doc-footer">
  @if(!empty($footerNote))
    <p class="footer-note">{{ $footerNote }}</p>
  @endif
  <p>{{ $settings['company_name'] ?? 'PURENEXT SARL' }}
    @if(!empty($settings['company_address'])) — {{ $settings['company_address'] }} @endif
    @if(!empty($settings['company_phone'])) — {{ $settings['company_phone'] }} @endif
  </p>
  @if(!empty($settings['company_rib']) && !empty($settings['company_bank']))
    <p>RIB : {{ $settings['company_rib'] }} — {{ $settings['company_bank'] }}</p>
  @endif
  <p>{{ $reference }}</p>
</div>