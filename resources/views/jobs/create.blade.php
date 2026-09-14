<x-layout>
    <x-page-heading>New Job</x-page-heading>

    <x-forms.form method="POST" action="/jobs">
        <x-forms.input label="Title" name="title" placeholder="CEO" />
        <x-forms.input label="Salary" name="salary" placeholder="$90,000 USD" />
        <x-forms.input label="Job Location" name="location" placeholder="123 Main St, Winter Park, FL 32789" autocomplete="street-address" />
        <input  name="latitude" id="latitude" value="{{ old('latitude') }}">
        <input  name="longitude" id="longitude" value="{{ old('longitude') }}">

        <div class="space-y-3">
            <p class="text-sm text-gray-400">Select the exact job location on the map.</p>
            @if(config('services.google_maps.key'))
                <div id="job-location-map" class="h-72 w-full overflow-hidden rounded-xl border border-white/10"></div>
                <p id="job-location-status" class="text-sm text-gray-400" aria-live="polite">Finding your location...</p>
            @else
                <p class="rounded-lg border border-yellow-400/30 bg-yellow-400/10 p-4 text-sm text-yellow-100">
                </p>
            @endif
        </div>

        <x-forms.select label="Schedule" name="schedule">
            <option>Part Time</option>
            <option>Full Time</option>
        </x-forms.select>

        <x-forms.input label="URL" name="url" placeholder="https://acme.com/jobs/ceo-wanted" />
        <x-forms.checkbox label="Feature (Costs Extra)" name="featured" />

        <x-forms.divider />

        <x-forms.input label="Tags (comma separated)" name="tags" placeholder="laracasts, video, education" />

        <x-forms.button>Publish</x-forms.button>
    </x-forms.form>
</x-layout>

@if(config('services.google_maps.key'))
    <script>
        function initJobMap() {
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const locationInput = document.getElementById('location');
            const locationStatus = document.getElementById('job-location-status');
            const defaultPosition = { lat: 28.5993, lng: -81.3392 };
            const hasCoordinates = latitudeInput.value !== '' && longitudeInput.value !== '';
            const position = hasCoordinates
                ? { lat: Number(latitudeInput.value), lng: Number(longitudeInput.value) }
                : defaultPosition;
            const map = new google.maps.Map(document.getElementById('job-location-map'), {
                center: position,
                zoom: hasCoordinates ? 15 : 11,
                mapTypeControl: false,
                streetViewControl: false,
            });
            const marker = new google.maps.Marker({
                map,
                position,
                draggable: true,
                title: 'Job location',
            });

            const setLocation = (coordinates) => {
                const latitude = coordinates.lat().toFixed(7);
                const longitude = coordinates.lng().toFixed(7);
                latitudeInput.value = latitude;
                longitudeInput.value = longitude;
                locationStatus.textContent = 'Finding the location name...';

                const mapTilerUrl = `https://api.maptiler.com/geocoding/${longitude},${latitude}.json?key={{ config('services.maptiler.key') }}`;
                fetch(mapTilerUrl)
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('MapTiler geocoding request failed');
                        }

                        return response.json();
                    })
                    .then((data) => {
                        if (!data.features || !data.features[0]) {
                            throw new Error('No location found');
                        }

                        locationInput.value = data.features[0].place_name;
                        locationStatus.textContent = 'Location selected.';
                    })
                    .catch(() => {
                        locationStatus.textContent = 'Coordinates selected, but the location name could not be found. Enter the location name manually.';
                    });
            };

            if (hasCoordinates) {
                setLocation(position);
            } else if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((currentPosition) => {
                    const currentLocation = {
                        lat: currentPosition.coords.latitude,
                        lng: currentPosition.coords.longitude,
                    };

                    map.setCenter(currentLocation);
                    marker.setPosition(currentLocation);
                    locationStatus.textContent = 'Map centered near your location. Click the map to choose the job location.';
                }, () => {
                    locationStatus.textContent = 'Location access was unavailable. Click the map to choose a location.';
                });
            } else {
                locationStatus.textContent = 'Click the map to choose a location.';
            }

            map.addListener('click', (event) => {
                marker.setPosition(event.latLng);
                setLocation(event.latLng);
            });
            marker.addListener('dragend', () => setLocation(marker.getPosition()));
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initJobMap"></script>
@endif
