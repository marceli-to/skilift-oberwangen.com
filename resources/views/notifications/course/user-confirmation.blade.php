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
    {{ $data['course_date'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>Vorname (Kind)</strong><br>
    {{ $data['firstname'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>Name (Kind)</strong><br>
    {{ $data['name'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>Geburtsdatum (Kind)</strong><br>
    {{ $data['dob'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>Vorname, Name (Erziehungsberechtigte)</strong><br>
    {{ $data['name_parents'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>Adresse</strong><br>
    {{ $data['street'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>PLZ/Ort</strong><br>
    {{ $data['location'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>Telefon</strong><br>
    {{ $data['phone'] }}
  </div>
  <br>
  <div class="text-base">
    <strong>E-Mail</strong><br>
    {{ $data['email'] }}
  </div>
  <br>
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
