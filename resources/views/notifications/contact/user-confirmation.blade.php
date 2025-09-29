<x-mail::message>
  <div class="text-base">
    Guten Tag<br><br>Besten Dank für Ihre Kontaktaufnahme. Wir werden uns so bald wie möglich bei Ihnen melden.<br><br>Freundliche Grüsse<br><br>Skilift Oberwangen, Oberwangen TG
  </div>
  <br><br>
  <div class="text-base">
    <strong>Ihre Angaben</strong><br>
  </div>
  <br>
  @if ($data['firstname'])
    <div class="text-base">
      <strong>Vorname</strong><br>
      {{ $data['firstname'] }}
    </div>
    <br>
  @endif
  @if ($data['name'])
    <div class="text-base">
      <strong>Name</strong><br>
      {{ $data['name'] }}
    </div>
    <br>
  @endif
  @if ($data['street'])
    <div class="text-base">
      <strong>Strasse, Nr.</strong><br>
      {{ $data['street'] }}
    </div>
    <br>
  @endif
  @if ($data['location'])
  <div class="text-base">
    <strong>PLZ / Ort</strong><br>
    {{ $data['location'] }}
  </div>
  <br>
@endif
  @if ($data['email'])
    <div class="text-base">
      <strong>E-Mail</strong><br>
      {{ $data['email'] }}
    </div>
    <br>
  @endif
  @if ($data['phone'])
    <div class="text-base">
      <strong>Telefon</strong><br>
      {{ $data['phone'] }}
    </div>
    <br>
  @endif
  @if ($data['message'])
  <div class="text-base">
      <strong>Mitteilung</strong><br>
      {!! nl2br($data['message']) !!}
    </div>
    <br>
  @endif
  <br>
</x-mail::message>
