<?php
// Konfigurasi database
$db['default'] = array(
    'dsn'       => '',
    'hostname'  => '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 1.1.1.248)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = dbora.arindo.net)))',
    'username'  => 'web_nasiraolap',
    'password'  => 'nas1raOlap',
    'database'  => '',
    'dbdriver'  => 'oci8',
    'dbprefix'  => '',
    'pconnect'  => FALSE,
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8',
    'dbcollat'  => 'utf8_general_ci',
    'swap_pre'  => '',
    'encrypt'   => FALSE,
    'compress'  => FALSE,
    'stricton'  => FALSE,
    'failover'  => array(),
    'save_queries' => TRUE
);

// Fungsi untuk mengeksekusi query dan menampilkan hasilnya
function run_query($query) {
    global $db;
    // Membuat koneksi ke database
    $connection = oci_connect($db['default']['username'], $db['default']['password'], $db['default']['hostname']);

    if (!$connection) {
        $error = oci_error();
        echo "Koneksi ke database gagal: " . $error['message'] . PHP_EOL;
        return;
    }

    $stid = oci_parse($connection, $query);

    if (!oci_execute($stid)) {
        $error = oci_error($stid);
        echo "Error menjalankan query: " . $error['message'] . PHP_EOL;
    } else {
        $ncols = oci_num_fields($stid);

        for ($i = 1; $i <= $ncols; $i++) {
            $colname = oci_field_name($stid, $i);
            echo str_pad($colname, 20);
        }
        echo PHP_EOL;

        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            foreach ($row as $item) {
                echo str_pad($item !== null ? $item : "NULL", 20);
            }
            echo PHP_EOL;
        }
    }

    // Menutup statement
    oci_free_statement($stid);
    // Menutup koneksi
    oci_close($connection);
}

// Membaca query dari input pengguna
echo "Masukkan query: ";
$query = trim(fgets(STDIN));

if (!empty($query)) {
    run_query($query);
} else {
    echo "Query tidak boleh kosong." . PHP_EOL;
}
?>
