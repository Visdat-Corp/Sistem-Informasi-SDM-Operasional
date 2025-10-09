let mapTambah, markerTambah;
let mapEdit, markerEdit;

// Function to initialize map for add modal
function initMapTambah(lat = -6.2088, lng = 106.8456) {
    if (mapTambah) return;
    mapTambah = L.map('mapTambah').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapTambah);

    markerTambah = L.marker([lat, lng], {draggable: true}).addTo(mapTambah);
    markerTambah.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('tambahLat').value = pos.lat;
        document.getElementById('tambahLng').value = pos.lng;
    });

    mapTambah.on('click', function(e) {
        markerTambah.setLatLng(e.latlng);
        document.getElementById('tambahLat').value = e.latlng.lat;
        document.getElementById('tambahLng').value = e.latlng.lng;
    });
}

// Function to initialize map for edit modal
function initMapEdit(lat = -6.2088, lng = 106.8456) {
    if (mapEdit) return;
    mapEdit = L.map('mapEdit').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapEdit);

    markerEdit = L.marker([lat, lng], {draggable: true}).addTo(mapEdit);
    markerEdit.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('editLat').value = pos.lat;
        document.getElementById('editLng').value = pos.lng;
    });

    mapEdit.on('click', function(e) {
        markerEdit.setLatLng(e.latlng);
        document.getElementById('editLat').value = e.latlng.lat;
        document.getElementById('editLng').value = e.latlng.lng;
    });
}

// Function to get current location
function getCurrentLocation(latInput, lngInput, marker) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            latInput.value = position.coords.latitude;
            lngInput.value = position.coords.longitude;
            if (marker) {
                marker.setLatLng([position.coords.latitude, position.coords.longitude]);
            }
        }, function(error) {
            alert('Error getting location: ' + error.message);
        });
    } else {
        alert('Geolocation is not supported by this browser.');
    }
}

// Event listeners for GPS buttons
document.getElementById('getLocationBtn').addEventListener('click', function() {
    const latInput = document.getElementById('tambahLat');
    const lngInput = document.getElementById('tambahLng');
    getCurrentLocation(latInput, lngInput, markerTambah);
});

document.getElementById('editGetLocationBtn').addEventListener('click', function() {
    const latInput = document.getElementById('editLat');
    const lngInput = document.getElementById('editLng');
    getCurrentLocation(latInput, lngInput, markerEdit);
});

// Edit button click handler
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const nama = this.getAttribute('data-nama');
        const lat = this.getAttribute('data-lat') || -6.2088;
        const lng = this.getAttribute('data-lng') || 106.8456;
        const radius = this.getAttribute('data-radius');

        document.getElementById('editForm').action = `/kelola-lokasi/${id}`;
        document.getElementById('editNama').value = nama;
        document.getElementById('editLat').value = lat;
        document.getElementById('editLng').value = lng;
        document.getElementById('editRadius').value = radius;

        // Initialize map for edit
        setTimeout(() => initMapEdit(lat, lng), 100);

        document.getElementById('editModal').classList.remove('hidden');
    });
});

// Delete button click handler
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        document.getElementById('deleteForm').action = `/kelola-lokasi/${id}`;
        document.getElementById('hapusModal').classList.remove('hidden');
    });
});

// Modal close handlers
document.querySelectorAll('.close-modal-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.fixed').classList.add('hidden');
    });
});

// Show add modal
document.getElementById('tambahBtn').addEventListener('click', function() {
    // Initialize map for add
    setTimeout(() => initMapTambah(), 100);
    document.getElementById('tambahModal').classList.remove('hidden');
});
