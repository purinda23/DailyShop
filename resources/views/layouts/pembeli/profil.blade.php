@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold text-dark mb-4">Profil Saya</h3>

    <ul class="nav nav-tabs custom-tabs mb-4" id="profileTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-secondary" id="data-tab" data-bs-toggle="tab" data-bs-target="#data-akun" type="button" role="tab" aria-controls="data-akun" aria-selected="true">
                <i class="bi bi-person-vcard me-2 text-primary"></i>Data Akun
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary" id="alamat-tab" data-bs-toggle="tab" data-bs-target="#alamat-saya" type="button" role="tab" aria-controls="alamat-saya" aria-selected="false">
                <i class="bi bi-geo-alt me-2 text-danger"></i>Alamat Saya
            </button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabContent">
        
        <div class="tab-pane fade show active" id="data-akun" role="tabpanel" aria-labelledby="data-tab">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <form action="/profil/update" method="POST" id="formProfil">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" id="inputNama" class="form-control bg-light input-profil" value="{{ $user->name ?? 'Purinda' }}" disabled required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" id="inputEmail" class="form-control bg-light input-profil" value="{{ $user->email ?? 'purinda04@gmail.com' }}" disabled required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Nomor Telepon</label>
                            <input type="text" name="telepon" id="inputTelepon" class="form-control bg-light input-profil" value="{{ $user->telepon ?? '087817239128' }}" disabled>
                        </div>
                    </div>
                    
                    <hr class="my-4 text-muted">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Terdaftar pada: {{ $user->created_at ? date('d', strtotime($user->created_at)) . ' ' . ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][date('n', strtotime($user->created_at))-1] . ' ' . date('Y', strtotime($user->created_at)) : '16 Juni 2026' }}</span>
                        
                        <div id="btnContainer">
                            <button type="button" id="btnEdit" class="btn btn-warning px-4 fw-bold rounded-3 text-white">
                                <i class="bi bi-pencil-square me-2"></i>Edit Profil
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-pane fade" id="alamat-saya" role="tabpanel" aria-labelledby="alamat-tab">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h5 class="fw-bold m-0 text-dark">Alamat Saya</h5>
                    <button class="btn btn-primary btn-sm rounded-2 px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAlamat">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Alamat Baru
                    </button>
                </div>
                
                <div class="address-list">
                    @if(!empty($user->alamat))
                        {{-- Memecah isi database menjadi array berdasarkan separator ||| --}}
                        @foreach(explode('|||', $user->alamat) as $index => $itemAlamat)
                            @php
                                // Parsing data alamat dengan pola regex [Label] Nama (Telepon) - Alamat Lengkap
                                preg_match('/\[(.*?)\]\s*(.*?)\s*\((.*?)\)\s*-\s*(.*)/', $itemAlamat, $matches);
                                
                                $labelAlamat     = isset($matches[1]) ? $matches[1] : 'Rumah';
                                $namaPenerima    = isset($matches[2]) ? $matches[2] : $user->name;
                                $teleponPenerima = isset($matches[3]) ? $matches[3] : $user->telepon;
                                $detailAlamat    = isset($matches[4]) ? $matches[4] : $itemAlamat;
                            @endphp

                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="fw-bold text-dark me-2 fs-6">{{ $namaPenerima }}</span>
                                            <span class="text-muted border-start ps-2 small">{{ $teleponPenerima }}</span>
                                        </div>
                                        
                                        <p class="text-secondary small mb-2">
                                            <span class="badge bg-light text-dark border me-1">{{ $labelAlamat }}</span> {{ $detailAlamat }}
                                        </p>
                                        
                                        @if($index == 0)
                                            <span class="badge border border-danger text-danger bg-transparent rounded-1 small py-1 px-2">Utama</span>
                                        @endif
                                    </div>
                                    
                                    <div class="text-end">
                                        <button class="btn btn-link text-primary text-decoration-none p-0 fw-semibold small" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalUbahAlamat"
                                                onclick="isiModalUbah('{{ $labelAlamat }}', '{{ addslashes($namaPenerima) }}', '{{ $teleponPenerima }}', '{{ addslashes($detailAlamat) }}', '{{ $index }}')">
                                            Ubah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-geo-alt fs-2 mb-2 d-block"></i>
                            <p class="mb-0 small">Belum ada alamat pengiriman yang disimpan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahAlamat" tabindex="-1" aria-labelledby="modalTambahAlamatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalTambahAlamatLabel">Tambah Alamat Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/profil/alamat/store" method="POST">
                @csrf
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Label Alamat</label>
                        <select name="label" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Label Alamat --</option>
                            <option value="Rumah">Rumah</option>
                            <option value="Kantor">Kantor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Penerima</label>
                        <input type="text" name="nama_penerima" class="form-control" placeholder="Masukkan nama penerima" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor Telepon Penerima</label>
                        <input type="text" name="telepon_penerima" class="form-control" placeholder="Contoh: 081234567xxx" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" class="form-control" rows="3" placeholder="Masukkan nama jalan, nomor rumah, RT/RW, kecamatan, dan kota" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Simpan Alamat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUbahAlamat" tabindex="-1" aria-labelledby="modalUbahAlamatLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalUbahAlamatLabel">Ubah Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/profil/alamat/update" method="POST">
                @csrf
                <input type="hidden" name="alamat_index" id="edit_index">
                
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Label Alamat</label>
                        <select name="label" id="edit_label" class="form-select" required>
                            <option value="Rumah">Rumah</option>
                            <option value="Kantor">Kantor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Penerima</label>
                        <input type="text" name="nama_penerima" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor Telepon Penerima</label>
                        <input type="text" name="telepon_penerima" id="edit_telepon" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" id="edit_alamat" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Logika Tombol Edit Profil Utama
        const btnEdit = document.getElementById('btnEdit');
        const btnContainer = document.getElementById('btnContainer');
        const inputs = document.querySelectorAll('.input-profil');

        if (btnEdit) {
            btnEdit.addEventListener('click', function () {
                inputs.forEach(input => {
                    input.removeAttribute('disabled');
                    input.classList.remove('bg-light'); 
                });

                btnContainer.innerHTML = `
                    <button type="button" id="btnBatal" class="btn btn-outline-secondary px-3 fw-bold rounded-3 me-2">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-3">Simpan Perubahan</button>
                `;

                document.getElementById('btnBatal').addEventListener('click', function() {
                    inputs.forEach(input => {
                        input.setAttribute('disabled', 'disabled');
                        input.classList.add('bg-light');
                    });
                    window.location.reload();
                });
            });
        }
    });

    // Logika pengisian otomatis value modal ubah alamat
    function isiModalUbah(label, nama, telepon, alamat, index) {
        document.getElementById('edit_label').value = label;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_telepon').value = telepon;
        document.getElementById('edit_alamat').value = alamat;
        document.getElementById('edit_index').value = index;
    }
</script>
@endsection