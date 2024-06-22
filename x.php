<?php
// Konfigurasi database
$db['default'] = array(
    'dsn'       => '',
    'hostname'  => '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 1.1.1.248)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = dbora.arindo.net)))',
        'username' => 'web_nasiraolap',
	'password' => 'nas1raOlap',
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

// Membuat koneksi ke database
$connection = oci_connect($db['default']['username'], $db['default']['password'], $db['default']['hostname']);

if (!$connection) {
    $error = oci_error();
    echo "Koneksi ke database gagal: " . $error['message'];
} else {
    // Menyiapkan dan menjalankan query untuk mendapatkan semua user schemas
    $query_schemas = "
        SELECT username
        FROM dba_users
        WHERE account_status = 'OPEN'
    ";
    $stid_schemas = oci_parse($connection, $query_schemas);

    echo "<h2>Daftar Database (User Schemas) dalam Instance Oracle</h2>";

    if (oci_execute($stid_schemas)) {
        echo "<table border='1'>\n";
        echo "<tr><th>USERNAME</th></tr>\n";
        while ($row = oci_fetch_array($stid_schemas, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<tr>\n";
            foreach ($row as $item) {
                echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
            }
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        $error = oci_error($stid_schemas);
        echo "Error menjalankan query schemas: " . $error['message'];
    }

    // Menutup statement
    oci_free_statement($stid_schemas);
}

// Menutup koneksi ketika selesai
oci_close($connection);
?>
