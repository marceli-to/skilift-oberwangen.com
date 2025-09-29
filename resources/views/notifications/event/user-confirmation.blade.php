<x-mail::message>
  <div class="text-base">
    {!! nl2br($data['text_user_confirmation_email']) !!}
  </div>
  <br><br>
  <div class="text-base">
    <strong>Ihre Angaben</strong><br>
  </div>
  <br>
  <div class="text-base">
    <strong>Veranstaltung</strong><br>
    {{ $data['title'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>Datum</strong><br>
    {{ $data['event_date'] }}
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
  @if ($data['street'])
    <div class="text-base">
      <strong>Strasse/Nr.</strong><br>
      {{ $data['street'] }}
    </div>
    <br>
  @endif
  @if ($data['zip'])
    <div class="text-base">
      <strong>Postleitzahl</strong><br>
      {{ $data['zip'] }}
    </div>
    <br>
  @endif
  @if ($data['location'])
    <div class="text-base">
      <strong>Ort</strong><br>
      {{ $data['location'] }}
    </div>
    <br>
  @endif
  @if ($data['number_people'])
    <div class="text-base">
      <strong>Anzahl Personen</strong><br>
      {{ $data['number_people'] }}
    </div>
    <br>
  @endif
  @if ($data['number_adults'])
    <div class="text-base">
      <strong>Anzahl Erwachsene</strong><br>
      {{ $data['number_adults'] }}
    </div>
    <br>
  @endif
  @if ($data['number_teenagers'])
    <div class="text-base">
      <strong>Anzahl Jugendliche</strong><br>
      {{ $data['number_teenagers'] }}
    </div>
    <br>
  @endif
  @if ($data['number_kids'])
    <div class="text-base">
      <strong>Anzahl Kinder</strong><br>
      {{ $data['number_kids'] }}
    </div>
    <br>
  @endif
  @if (isset($data['cost_total']))
    <div class="text-base">
      <strong>Kosten</strong><br>
      CHF {{ number_format($data['cost_total'], 2, '.', '') }}
    </div>
    <br>
  @endif
  @if (isset($data['state']) && $data['state'] === 'waitinglist')
    <div class="text-base">
      <strong>Status</strong><br>
      Sie sind zur Zeit auf der Warteliste.
    </div>
    <br>
  @endif
  @if ($data['remarks'])
    <div class="text-base">
      <strong>Bemerkungen</strong><br>
      {{ nl2br($data['remarks']) }}
    </div>
    <br>
  @endif
</x-mail::message>
