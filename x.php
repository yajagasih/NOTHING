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

function executeQuery($connection, $query) {
    $stid = oci_parse($connection, $query);
    if (!$stid) {
        $error = oci_error($connection);
        echo "Error parsing query: " . $error['message'] . "\n";
        return;
    }

    $isSelect = stripos(trim($query), 'select') === 0;

    if (oci_execute($stid)) {
        if ($isSelect) {
            $ncols = oci_num_fields($stid);
            echo "<table border='1'>\n<tr>\n";
            for ($i = 1; $i <= $ncols; $i++) {
                $colname = oci_field_name($stid, $i);
                echo "<th>" . htmlentities($colname, ENT_QUOTES) . "</th>\n";
            }
            echo "</tr>\n";
            while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                echo "<tr>\n";
                foreach ($row as $item) {
                    echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
                }
                echo "</tr>\n";
            }
            echo "</table>\n";
        } else {
            echo "Query berhasil dijalankan.\n";
        }
    } else {
        $error = oci_error($stid);
        echo "Error menjalankan query: " . $error['message'] . "\n";
    }

    oci_free_statement($stid);
}

do {
    $connection = oci_connect($db['default']['username'], $db['default']['password'], $db['default']['hostname']);

    if (!$connection) {
        $error = oci_error();
        echo "Koneksi ke database gagal: " . $error['message'] . "\n";
        exit;
    }

    echo "Masukkan query: ";
    $handle = fopen("php://stdin", "r");
    $query = trim(fgets($handle));

    executeQuery($connection, $query);

    echo "Ingin menjalankan query lagi? (y/n): ";
    $again = trim(fgets($handle));

    oci_close($connection);
} while (strtolower($again) === 'y');

echo "Program selesai.\n";
?>
