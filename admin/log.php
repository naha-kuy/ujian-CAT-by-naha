<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log Aktivitas Siswa</title>
    <?php include '../inc/css.php'; ?>
    <link rel="stylesheet" href="../assets/datatables/buttons.bootstrap5.min.css" />
    <style>
    .table-wrapper {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }

    table th,
    table td {
        text-align: left !important;
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <?php include 'navbar.php'; ?>
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h5 class="card-title mb-0">Log Aktivitas Siswa</h5>
                                    <button id="exportExcel" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-excel"></i> Export Excel</button>
                                </div>
                                <div class="card-body">
                                    <div class="table-wrapper">
                                        <table id="logTable" class="table table-striped nowrap" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Kelas</th>
                                                    <th>Last Activity</th>
                                                    <th>Page URL</th>
                                                    <th>Session Token</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                $query = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY last_activity DESC");
                                                while ($row = mysqli_fetch_assoc($query)) {
                                                    echo '<tr>';
                                                    echo '<td>' . $no++ . '</td>';
                                                    echo '<td>' . htmlspecialchars($row['nama_siswa']) . '</td>';
                                                    echo '<td>' . htmlspecialchars($row['kelas']).''. htmlspecialchars($row['rombel']) . '</td>';
                                                    echo '<td>' . htmlspecialchars($row['last_activity']) . '</td>';
                                                    echo '<td>' . htmlspecialchars($row['page_url']) . '</td>';
                                                    echo '<td>' . htmlspecialchars($row['session_token']) . '</td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../inc/js.php'; ?>
    <script src="../assets/datatables/dataTables.buttons.min.js"></script>
<script src="../assets/datatables/buttons.bootstrap5.min.js"></script>
<script src="../assets/datatables/jszip.min.js"></script>
<script src="../assets/datatables/buttons.html5.min.js"></script>
    <script>
    const table = $('#logTable').DataTable({
    dom: 'frtip',
    buttons: [{
        extend: 'excelHtml5',
        title: 'Log Aktivitas Siswa',
        text: 'Export Excel',
        exportOptions: {
            columns: [0, 1, 2, 3, 4]
        }
    }],
    paging: true,
    responsive: true
});

// Jika mau tombol luar tetap pakai trigger
$('#exportExcel').on('click', function () {
    table.button('.buttons-excel').trigger();
});

    </script>
</body>

</html>