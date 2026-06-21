@extends('layouts.app')

@section('title', 'Pendaftaran Magang')

@section('styles')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Ukuran Peta Diperkecil & Responsif */
        #map {
            height: 280px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
            z-index: 1;
        }

        /* Styling Hasil Pencarian (Dropdown) */
        .search-container {
            position: relative;
            width: 100%;
        }

        #search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 9999;
            background: white;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .search-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.85rem;
            transition: background-color 0.2s;
        }

        .search-item:hover {
            background-color: #f8f9fa;
            color: #004b23;
        }

        .search-item:last-child {
            border-bottom: none;
        }

        .search-item strong {
            display: block;
            color: #333;
            font-weight: 600;
        }

        .search-item small {
            color: #777;
            display: block;
            margin-top: 2px;
            font-size: 0.75rem;
        }
        
        /* Form Label Compact */
        .form-label {
            margin-bottom: 0.2rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        /* Custom Input Size */
        .form-control-sm, .form-select-sm {
            font-size: 0.9rem;
            padding: 0.4rem 0.6rem;
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-11">

            <div class="card shadow-lg border-0 rounded-3">                
                {{-- Card Header --}}
                <div class="card-header bg-primary text-white py-3 rounded-top-3">
                    <h6 class="mb-0 fw-bold d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill me-2 fs-5"></i> Form Pendaftaran Magang
                    </h6>
                </div>
                
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 mb-4 border-0 shadow-sm bg-danger-soft">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('magang.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            
                            {{-- ===================================
                                 KOLOM KIRI: PETA & LOKASI
                                 =================================== --}}
                            <div class="col-lg-5 mb-4 mb-lg-0 border-end pe-lg-4">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="bi bi-map me-2"></i>Lokasi Perusahaan
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label text-danger">Cari Lokasi / Nama Tempat</label>
                                    <div class="search-container">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" id="location-search" class="form-control border-start-0"
                                                placeholder="Ketik nama perusahaan..." autocomplete="off">
                                        </div>
                                        <div id="search-results"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div id="map" class="shadow-sm"></div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted" style="font-size: 0.7rem;">OpenStreetMap</small>
                                        <small class="text-primary fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-info-circle"></i> Geser pin 📍 untuk akurasi posisi.
                                        </small>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="small text-muted mb-0" style="font-size: 0.75rem;">Latitude</label>
                                        <input type="text" id="latitude" name="latitude"
                                            class="form-control form-control-sm bg-light text-secondary" readonly required>
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted mb-0" style="font-size: 0.75rem;">Longitude</label>
                                        <input type="text" id="longitude" name="longitude"
                                            class="form-control form-control-sm bg-light text-secondary" readonly required>
                                    </div>
                                </div>
                            </div>

                            {{-- ===================================
                                 KOLOM KANAN: FORM DETAIL
                                 =================================== --}}
                            <div class="col-lg-7 ps-lg-4">
                                <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">
                                    <i class="bi bi-file-earmark-text me-2"></i>Detail Magang
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control form-control-sm"
                                        placeholder="Otomatis terisi dari peta atau ketik manual" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tema Magang <span class="text-danger">*</span></label>
                                    <input type="text" name="tema_magang" id="tema_magang" class="form-control form-control-sm"
                                        placeholder="Masukan Tema Magang." required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea name="alamat" id="alamat" class="form-control form-control-sm" rows="2"
                                        placeholder="Jalan, Kota, Provinsi..." required></textarea>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Kategori Industri</label>
                                        <select name="kategori_industri" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="IT">IT</option>
                                            <option value="BUMN">BUMN</option>
                                            <option value="Pemerintahan">Pemerintahan</option>
                                            <option value="Start-up">Start-up</option>
                                            <option value="Manufaktur">Manufaktur</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Gaji</label>
                                        <select name="status_gaji" class="form-select form-select-sm" required>
                                            <option value="paid">Paid (Digaji)</option>
                                            <option value="unpaid">Unpaid (Sukarela)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Dosen Pembimbing <span class="text-danger">*</span></label>
                                    <select name="dosen_id" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>-- Pilih Dosen --</option>
                                        @foreach($daftar_dosen as $dosen)
                                            <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text small">Diskusikan dengan dosen sebelum memilih.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Surat penyerahan/TTD Kaprodi <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="file" name="file_surat" class="form-control" accept=".pdf" required>
                                        <span class="input-group-text"><i class="bi bi-file-earmark-pdf"></i></span>
                                    </div>
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">
                                        Format PDF Only. Maksimal ukuran file 2MB.
                                    </div>
                                </div>

                                <div class="d-grid pt-2">
                                    <button type="submit" class="btn btn-primary fw-bold py-2">
                                        <i class="bi bi-send-check-fill me-2"></i> Simpan
                                    </button>
                                </div>

                            </div> {{-- End Col Kanan --}}
                        </div> {{-- End Row --}}
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Scripts Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // --- API KEY TOMTOM ---
        // Teks 'YOUR_' yang salah ketik sebelumnya sudah dihapus agar valid
        const TOMTOM_KEY = 'TNCYx1oO4bMYf9BPy221xbRUb1xQvo0d';

        // 1. SETUP PETA LEAFLET (Peta visual menggunakan OSM gratis)
        var defaultLat = -7.8113; // Default Jogja
        var defaultLng = 110.3235;

        var map = L.map('map').setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        function updateInput(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        // Setup awal saat halaman pertama kali di-load
        updateInput(defaultLat, defaultLng);
        reverseGeocode(defaultLat, defaultLng);

        marker.on('dragend', function (e) {
            var pos = marker.getLatLng();
            updateInput(pos.lat, pos.lng);
            reverseGeocode(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateInput(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        // --- 2. FITUR PENCARIAN DENGAN TOMTOM SEARCH API ---
        const searchInput = document.getElementById('location-search');
        const resultsContainer = document.getElementById('search-results');
        const companyNameInput = document.getElementById('nama_perusahaan');
        const addressInput = document.getElementById('alamat');
        let timeout = null;

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            const query = this.value.trim();

            if (query.length < 3) {
                resultsContainer.style.display = 'none';
                return;
            }

            // Memberi jeda 500ms agar tidak buang-buang kuota API
            timeout = setTimeout(() => {
                const url = `https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_KEY}&countrySet=ID&lat=${defaultLat}&lon=${defaultLng}&limit=5`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        resultsContainer.innerHTML = '';
                        resultsContainer.style.display = 'block';

                        // JIKA HASIL DITEMUKAN DI DATABASE
                        if (data.results && data.results.length > 0) {
                            data.results.forEach(place => {
                                // Optional Chaining untuk mencegah aplikasi crash jika atribut kosong
                                let shortName = place?.poi?.name || place?.address?.streetName || place?.address?.municipality || "Lokasi Tanpa Nama";
                                let fullName = place?.address?.freeformAddress || place?.address?.countrySecondarySubdivision || shortName;

                                const item = document.createElement('div');
                                item.className = 'search-item';
                                item.innerHTML = `<strong>${shortName}</strong><small>${fullName}</small>`;

                                item.addEventListener('click', () => {
                                    const lat = place.position.lat;
                                    const lon = place.position.lon;

                                    map.setView([lat, lon], 16);
                                    marker.setLatLng([lat, lon]);
                                    updateInput(lat, lon);

                                    companyNameInput.value = shortName;
                                    addressInput.value = fullName;
                                    searchInput.value = shortName;

                                    resultsContainer.style.display = 'none';
                                });
                                resultsContainer.appendChild(item);
                            });
                        } else {
                            // SOLUSI UX: JIKA LOKASI TIDAK ADA DI DATABASE TOMTOM
                            const notFoundItem = document.createElement('div');
                            notFoundItem.className = 'search-item bg-light text-center';
                            notFoundItem.innerHTML = `
                                <strong class="text-danger small">📍 Nama tempat tidak terdaftar di peta</strong>
                                <small class="text-muted mt-1 d-block">Klik di sini, lalu ketik nama manual & geser pin peta langsung ke lokasinya.</small>
                            `;
                            
                            // Jika diklik, otomatis masukkan input pencarian ke form nama perusahaan, lalu fokuskan
                            notFoundItem.addEventListener('click', () => {
                                companyNameInput.value = searchInput.value;
                                resultsContainer.style.display = 'none';
                                companyNameInput.focus();
                            });

                            resultsContainer.appendChild(notFoundItem);
                        }
                    })
                    .catch(err => {
                        console.error("TomTom Search Error:", err);
                        resultsContainer.style.display = 'none';
                    });
            }, 500);
        });

        // --- 3. REVERSE GEOCODING DENGAN TOMTOM ---
        function reverseGeocode(lat, lng) {
            const url = `https://api.tomtom.com/search/2/reverseGeocode/${lat},${lng}.json?key=${TOMTOM_KEY}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.addresses && data.addresses.length > 0) {
                        const addressObj = data.addresses[0].address;
                        const fullAddress = addressObj?.freeformAddress || "";
                        
                        addressInput.value = fullAddress;

                        // Jika nama perusahaan kosong / tidak diubah manual, otomatis isi dengan nama jalan/area terpendek
                        if (!companyNameInput.value || companyNameInput.value.trim() === '' || companyNameInput.value === 'Lokasi Tanpa Nama') {
                            companyNameInput.value = addressObj?.streetName || addressObj?.municipality || fullAddress.split(',')[0] || "";
                        }
                    }
                })
                .catch(err => console.log("Reverse geo error", err));
        }

        // Menutup dropdown jika user mengklik area luar
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.style.display = 'none';
            }
        });
    </script>
@endsection