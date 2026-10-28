<?php
  require_once __DIR__ . "/Koneksi.php";

  // Tangkap input pencarian dan filter
  $search_nama = $_GET['search_nama'] ?? '';
  $filter_predikat = $_GET['filter_predikat'] ?? '';

  // Query dasar
  $sql = "SELECT * FROM nilaimhs WHERE 1=1";

  // Tambahkan filter nama jika diisi
  if ($search_nama !== '') {
      $nama_aman = mysqli_real_escape_string($conn, $search_nama);
      $sql .= " AND nama_lengkap LIKE '%$nama_aman%'";
  }

  // Tambahkan filter predikat jika dipilih
  if ($filter_predikat !== '') {
      $predikat_aman = mysqli_real_escape_string($conn, $filter_predikat);
      $sql .= " AND predikat = '$predikat_aman'";
  }

  // Urutkan dari data yang paling baru ditambahkan
  $sql .= " ORDER BY reg_date DESC";

  $result = mysqli_query($conn, $sql);
?>

<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE 4 | Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <link rel="preload" href="./css/adminlte.css" as="style" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="./css/adminlte.css" />

    <!-- DataTables CSS Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" />
  </head>
  <!--end::Head-->
  
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
      
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a>
            </li>
          </ul>
        </div>
      </nav>
      <!--end::Header-->

      <!--begin::App Main-->
      <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h1 class="mb-0 fs-3">Data Nilai Mahasiswa</h1></div>
              <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nilai</li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <div class="app-content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                
                <!-- BEGIN: Card -->
                <div class="card mb-4">
                  <div class="card-header">
                    <div class="row g-2 align-items-center">
                      <div class="col-12 col-md-4">
                        <h3 class="card-title">Direktori Nilai</h3>
                      </div>
                      
                      <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                          
                          <!-- BEGIN: Form Search & Filter -->
                          <form method="GET" action="" class="d-flex gap-2 mb-0">
                            <!-- Input Search -->
                            <div class="input-group input-group-sm w-auto">
                              <span class="input-group-text"><i class="bi bi-search"></i></span>
                              <input type="search" name="search_nama" class="form-control" placeholder="Cari Berdasarkan Nama" style="width: 180px" value="<?php echo htmlspecialchars($search_nama); ?>" />
                            </div>

                            <!-- Dropdown Filter -->
                            <!-- Dropdown Filter Predikat -->
                            <select name="filter_predikat" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                              <option value="">Semua Predikat</option>
                              <option value="Sangat Memuaskan" <?php if($filter_predikat == 'Sangat Memuaskan') echo 'selected'; ?>>Sangat Memuaskan</option>
                              <option value="Memuaskan" <?php if($filter_predikat == 'Memuaskan') echo 'selected'; ?>>Memuaskan</option>
                              <option value="Cukup Memuaskan" <?php if($filter_predikat == 'Cukup Memuaskan') echo 'selected'; ?>>Cukup Memuaskan</option>
                              <option value="Kurang Memuaskan" <?php if($filter_predikat == 'Kurang Memuaskan') echo 'selected'; ?>>Kurang Memuaskan</option>
                              <option value="Tidak Lulus" <?php if($filter_predikat == 'Tidak Lulus') echo 'selected'; ?>>Tidak Lulus</option>
                            </select>
                            
                            <button type="submit" class="d-none"></button>
                          </form>
                          <!-- END: Form Search & Filter -->

                          <!-- Tombol Tambah Data -->
                          <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add-user">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah Data Nilai
                          </button>
                          
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="card-body p-3">
                    <div class="table-responsive">
                      <table id="table-nilai" class="table table-hover align-middle m-0" style="width:100%">
                        <thead>
                          <tr>
                            <th>NIM</th>
                            <th class="text-center">Foto</th>
                            <th>Nama Lengkap</th>
                            <th>Nilai Akhir</th>
                            <th>Nilai Huruf</th>
                            <th>Predikat</th>
                            <th>Tanggal Buat</th>
                            <th class="text-end">Actions</th>
                          </tr>
                        </thead>
                        
                        <tbody>
                          <?php 
                          if (mysqli_num_rows($result) > 0) {
                              while($row = mysqli_fetch_assoc($result)) {
                                  // Pengecekan Foto berdasarkan NIM (.png / .jpg / avatar default)
                                  $foto_mhs = "./assets/img/" . $row['nim'] . ".png";
                                  if (!file_exists($foto_mhs)) {
                                      $foto_mhs = "./assets/img/" . $row['nim'] . ".jpg";
                                  }
                                  if (!file_exists($foto_mhs)) {
                                      $foto_mhs = "./assets/img/avatar.png";
                                  }
                          ?>
                          <tr>
                            <td><span class="fw-medium"><?php echo htmlspecialchars($row['nim']); ?></span></td>            
                            
                            <!-- Kolom Foto -->
                            <td class="text-center">
                              <img src="<?php echo $foto_mhs; ?>" alt="Foto" class="img-size-32 rounded-circle" />
                            </td>

                            <!-- Kolom Nama Lengkap -->
                            <td><span class="fw-medium"><?php echo htmlspecialchars($row['nama_lengkap']); ?></span></td>
                            
                            <td><span class="fw-medium"><?php echo htmlspecialchars($row['nilai_akhir']); ?></span></td>
                            <td><span class="fw-medium"><?php echo htmlspecialchars($row['nilai_huruf']); ?></span></td>
                            <td><span class="fw-medium"><?php echo htmlspecialchars($row['predikat']); ?></span></td>
                            <td><span class="fw-medium"><?php echo htmlspecialchars($row['reg_date']); ?></span></td>
                            
                            <!-- BEGIN: Tombol Actions -->
                            <td class="text-end">
                              <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modal-edit-user-<?php echo $row['nim']; ?>"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-delete-user-<?php echo $row['nim']; ?>"><i class="bi bi-trash"></i></button>
                              </div>

                              <!-- Modal Edit User -->
                              <div class="modal fade text-start" id="modal-edit-user-<?php echo $row['nim']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <form action="update_user.php" method="POST">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Edit Nilai - <?php echo htmlspecialchars($row['nama_lengkap']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        <div class="mb-3">
                                          <label class="form-label">NIM</label>
                                          <input type="number" class="form-control" name="nim" value="<?php echo htmlspecialchars($row['nim']); ?>" readonly />
                                        </div>
                                        <div class="mb-3">
                                          <label class="form-label">Nama Lengkap</label>
                                          <input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($row['nama_lengkap']); ?>" required />
                                        </div>
                                        <div class="mb-3">
                                          <label class="form-label">Nilai Akhir</label>
                                          <input type="number" class="form-control" name="nilai_akhir" value="<?php echo htmlspecialchars($row['nilai_akhir']); ?>" min="0" max="100" step="0.01" required />
                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>

                              <!-- Modal Hapus User -->
                              <div class="modal fade text-start" id="modal-delete-user-<?php echo $row['nim']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                  <div class="modal-content">
                                    <form action="hapus_user.php" method="POST">
                                      <div class="modal-header">
                                        <h5 class="modal-title text-danger">Konfirmasi Hapus Data</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus data nilai mahasiswa <strong><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong>?</p>
                                        <input type="hidden" name="nim" value="<?php echo $row['nim']; ?>">
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Ya, Hapus Data</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>

                            </td>
                            <!-- END: Tombol Actions -->

                          </tr>
                          <?php
                              } // Penutup while loop
                          } else {
                              echo "<tr><td colspan='8' class='text-center'>Tidak ada data ditemukan</td></tr>";
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <!-- END: Card -->

              </div>
            </div>

            <!-- Modal Tambah Data Baru -->
          <div class="modal fade" id="modal-add-user" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                
                <!-- WAJIB ADA: enctype="multipart/form-data" -->
                <form action="simpan_user.php" method="POST" enctype="multipart/form-data">
                  <div class="modal-header">
                    <h5 class="modal-title">Tambahkan Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">NIM</label>
                      <input type="number" class="form-control" name="nim" placeholder="Contoh: 123456789" required />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Nama Lengkap</label>
                      <input type="text" class="form-control" name="nama_lengkap" placeholder="Contoh: Hilmi Andika" required />
                    </div>
                    
                    <!-- Input Upload Foto -->
                    <div class="mb-3">
                      <label class="form-label">Foto Profil (Opsional)</label>
                      <input type="file" class="form-control" name="foto" accept="image/*" />
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Nilai Akhir</label>
                      <input type="number" class="form-control" name="nilai_akhir" placeholder="Contoh: 90" min="0" max="100" step="0.01" required />
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Nilai</button>
                  </div>
                </form>

              </div>
            </div>
          </div>
            
          </div>
        </div>
      </main>

      <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">
          Pemograman Web - Semester 5 - Desain Menggunakan AdminLTE.io.
        </div>
        <strong>Hak Cipta &copy; Pada Prodi TI - 2026&nbsp;</strong> Seluruh hak cipta dilindungi.
      </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="./js/adminlte.js"></script>

    <!-- jQuery & DataTables Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Inisialisasi DataTables -->
    <!-- Inisialisasi DataTables dengan Pagination Khusus -->
      <script>
  $(document).ready(function() {
    $('#table-nilai').DataTable({
      "lengthMenu": [[5, 10, 15], [5, 10, 15]],
      "pageLength": 5,
      "searching": false, 
      "ordering": true,   
      "order": [],        
      "pagingType": "full_numbers",
      
      // MENGATUR SORTING HANYA UNTUK NILAI AKHIR (Indeks 3)
      "columnDefs": [
        { "orderable": false, "targets": [0, 1, 2, 4, 5, 6, 7] } // Mematikan ikon panah/sorting di kolom selain Nilai Akhir
      ],
      
      "language": {
        "lengthMenu": "Tampilkan _MENU_ data",
        "zeroRecords": "Data tidak ditemukan",
        "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        "infoEmpty": "Tidak ada data",
        "paginate": {
          "first": "&laquo;&laquo;",
          "previous": "&laquo;",
          "next": "&raquo;",
          "last": "&raquo;&raquo;"
        }
      }
    });
  });
</script>
  </body>
</html>