
<div style="height: 500px">
<gmp-map center="{{ $getState()['longitude'] }}, {{ $getState()['latitude'] }}" zoom="15" map-id="{{ config('app.google-maps.id') }}">
    <gmp-advanced-marker position="{{ $getState()['longitude'] }}, {{ $getState()['latitude'] }}" title="Uluru" gmp-clickable></gmp-advanced-marker>
</gmp-map>
<script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google-maps.key') }}&callback=initMap&libraries=marker&v=beta&solution_channel=GMP_CCS_infowindows_v2"
        defer
></script>
</div>
