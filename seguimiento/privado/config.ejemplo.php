<?php
/**
 * Configuración del portal de seguimiento.
 *
 * EN EL SERVIDOR: copiar este archivo como «config.php» en esta misma carpeta y pegar el secreto de
 * la integración de Notion. config.php NO va a GitHub ni en el ZIP de la web: así una actualización
 * de la página nunca borra el secreto.
 *
 * La integración de Notion debe ser de SOLO LECTURA y estar conectada únicamente a las bases
 * Portal_Clientes y Portal_Hitos.
 */
return [
    // Notion → Integraciones → «Portal web Mesa Urbana» → Secreto de integración interna
    'notion_token' => 'PEGAR_AQUI_EL_SECRETO_DE_NOTION',

    // Fuentes de datos de Notion (no son secretas; no cambiar)
    'ds_portal_clientes' => '91b3deca-599f-465f-abf1-42356732960a',
    'ds_portal_hitos' => 'a9c6c6ff-702d-4a93-bed8-21d0e2d69073',

    // Minutos que la página guarda una copia antes de volver a consultar Notion
    'minutos_cache' => 5,
];
