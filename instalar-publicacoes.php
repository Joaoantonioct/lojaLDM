<?php

$files = [
    'app/Http/Controllers/PdvSyncController.php',
    'app/Http/Controllers/AsaasWebhookController.php',
];

foreach ($files as $file) {
    if (!is_file($file)) {
        fwrite(STDERR, "ERRO: arquivo não encontrado: $file\n");
        exit(1);
    }

    echo "ARQUIVO LOCAL: $file\n";
}

echo "\nVerificação local concluída.\n";
