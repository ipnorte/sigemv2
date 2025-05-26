<table border="1">
    <thead>
        <tr>
            <th>Archivo Backup</th>
            <th>Tamaño</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ficheros as $file): ?>
            <tr>
                <td>
                    <a href="<?php echo $this->base ?>/seguridad/backups/download/<?php echo h($file) ?>">
                        <?php echo h($file) ?>
                    </a>
                </td>
                <td style="text-align: right;">
                    <?php 
                        $filePath = rtrim($pathMySQL, '/') . '/' . $file;
                        if (file_exists($filePath) && is_readable($filePath)) {
                            $fileSize = filesize($filePath);
                            echo number_format($fileSize / (1024 * 1024), 2) . ' MB';
                        } else {
                            echo "No disponible";
                        }
                    ?>
                </td>
                <td>
                    <?php 
                        if (file_exists($filePath) && is_readable($filePath)) {
                            $fileTime = filemtime($filePath);
                            echo date("Y-m-d H:i:s", $fileTime);
                        } else {
                            echo "Fecha desconocida";
                        }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
