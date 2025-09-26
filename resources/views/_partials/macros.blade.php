@php
  $width = $width ?? 'auto';
  $height = $height ?? '24';
@endphp

<span class="text-primary">
  <img src="/img/logo.png" style="max-width: 100%" width="{{ $width }}" height="{{ $height }}" >
</span>
