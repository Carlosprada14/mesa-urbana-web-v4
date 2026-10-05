<?php
/**
 * Portal de seguimiento para clientes — Mesa Urbana S.A.S.
 *
 * Enlace del cliente: https://mesaurbanaarquitectos.com/seguimiento/?c=CLAVE
 *
 * Lee de Notion SOLO las bases Portal_Clientes y Portal_Hitos (la integración no tiene acceso a
 * nada más), toma únicamente los campos pensados para el cliente y los pinta con
 * privado/plantilla.html. Guarda una copia por unos minutos para no consultar Notion en cada visita.
 * Configuración (token de Notion) en privado/config.php, que nunca va a GitHub.
 */
declare(strict_types=1);

ini_set('display_errors', '0');
header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('Referrer-Policy: no-referrer');
header('Cache-Control: private, no-store, max-age=0');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

const DIR_PRIVADO = __DIR__ . '/privado';
const NOTION_VERSION = '2025-09-03';

// ---------------------------------------------------------------- salida

function pintar(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    $plantilla = @file_get_contents(DIR_PRIVADO . '/plantilla.html');
    if ($plantilla === false) {
        echo 'Página no disponible en este momento.';
        exit;
    }
    $json = json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );
    echo str_replace('__DATOS_JSON__', $json === false ? 'null' : $json, $plantilla);
    exit;
}

function error_portal(int $codigo, string $titulo, string $texto): void
{
    pintar(['error' => ['titulo' => $titulo, 'texto' => $texto]], $codigo);
}

function responder(array $datos): void
{
    if (!empty($datos['no_existe'])) {
        error_portal(404, 'Enlace no disponible', 'Este enlace no está activo. Si cree que es un error, escríbanos y le enviamos uno nuevo.');
    }
    unset($datos['_guardado']);
    pintar($datos);
}

// ---------------------------------------------------------------- Notion

function notion_consultar(string $token, string $fuente, array $cuerpo, int $maximo = 500): array
{
    $resultados = [];
    $cursor = null;
    do {
        if ($cursor !== null) {
            $cuerpo['start_cursor'] = $cursor;
        }
        $ch = curl_init('https://api.notion.com/v1/data_sources/' . rawurlencode($fuente) . '/query');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Notion-Version: ' . NOTION_VERSION,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => (string)json_encode($cuerpo),
        ]);
        $respuesta = curl_exec($ch);
        $codigo = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorCurl = curl_error($ch);
        unset($ch);

        if ($respuesta === false) {
            throw new RuntimeException('curl: ' . $errorCurl);
        }
        $json = json_decode((string)$respuesta, true);
        if ($codigo !== 200 || !is_array($json)) {
            $detalle = is_array($json) ? (($json['code'] ?? '') . ' ' . ($json['message'] ?? '')) : substr((string)$respuesta, 0, 200);
            throw new RuntimeException('Notion HTTP ' . $codigo . ': ' . $detalle);
        }
        foreach (($json['results'] ?? []) as $fila) {
            $resultados[] = $fila;
        }
        $cursor = (!empty($json['has_more']) && !empty($json['next_cursor'])) ? (string)$json['next_cursor'] : null;
    } while ($cursor !== null && count($resultados) < $maximo);

    return $resultados;
}

// Lectores de propiedades: devuelven solo texto plano o fechas, nunca el objeto de Notion.

function texto(?array $prop): string
{
    $tipo = $prop['type'] ?? '';
    if ($tipo !== 'title' && $tipo !== 'rich_text') {
        return '';
    }
    $salida = '';
    foreach (($prop[$tipo] ?? []) as $trozo) {
        $salida .= (string)($trozo['plain_text'] ?? '');
    }
    return trim($salida);
}

function opcion(?array $prop): string
{
    $tipo = $prop['type'] ?? '';
    if (($tipo === 'select' || $tipo === 'status') && !empty($prop[$tipo]['name'])) {
        return (string)$prop[$tipo]['name'];
    }
    return '';
}

function fecha(?array $prop): ?array
{
    if (($prop['type'] ?? '') !== 'date' || empty($prop['date']['start'])) {
        return null;
    }
    return [
        'inicio' => (string)$prop['date']['start'],
        'fin' => !empty($prop['date']['end']) ? (string)$prop['date']['end'] : null,
    ];
}

function enlace(?array $prop): string
{
    $url = (($prop['type'] ?? '') === 'url') ? (string)($prop['url'] ?? '') : '';
    return preg_match('#^https?://#i', $url) ? $url : '';
}

function cargar_desde_notion(array $config, string $clave): array
{
    $token = (string)$config['notion_token'];

    $proyectos = notion_consultar($token, (string)$config['ds_portal_clientes'], [
        'filter' => ['and' => [
            ['property' => 'Clave', 'rich_text' => ['equals' => $clave]],
            ['property' => 'Activo', 'checkbox' => ['equals' => true]],
        ]],
        'page_size' => 1,
    ], 1);
    if (!$proyectos) {
        return ['no_existe' => true];
    }

    $proyecto = $proyectos[0];
    $pp = $proyecto['properties'] ?? [];
    $actualizado = (string)($proyecto['last_edited_time'] ?? '');

    $filas = notion_consultar($token, (string)$config['ds_portal_hitos'], [
        'filter' => ['and' => [
            ['property' => 'Proyecto', 'relation' => ['contains' => (string)$proyecto['id']]],
            ['property' => 'Visible', 'checkbox' => ['equals' => true]],
        ]],
        'sorts' => [['property' => 'Fecha', 'direction' => 'ascending']],
        'page_size' => 100,
    ]);

    $hitos = [];
    foreach ($filas as $fila) {
        $hp = $fila['properties'] ?? [];
        $hitos[] = [
            'titulo' => texto($hp['Hito'] ?? null),
            'tipo' => opcion($hp['Tipo'] ?? null),
            'estado' => opcion($hp['Estado'] ?? null),
            'fecha' => fecha($hp['Fecha'] ?? null),
            'detalle' => texto($hp['Detalle'] ?? null),
            'enlace' => enlace($hp['Enlace'] ?? null),
        ];
        $editado = (string)($fila['last_edited_time'] ?? '');
        if ($editado > $actualizado) {
            $actualizado = $editado;
        }
    }

    return [
        'proyecto' => [
            'nombre' => texto($pp['Portal'] ?? null),
            'cliente' => texto($pp['Cliente'] ?? null),
            'servicio' => texto($pp['Servicio'] ?? null),
            'ubicacion' => texto($pp['Ubicación'] ?? null),
            'estado' => opcion($pp['Estado'] ?? null),
            'bienvenida' => texto($pp['Bienvenida'] ?? null),
            'resumen' => texto($pp['Resumen'] ?? null),
            'responsable' => texto($pp['Responsable'] ?? null),
        ],
        'hitos' => $hitos,
        'actualizado' => $actualizado,
    ];
}

// ---------------------------------------------------------------- petición

$archivoConfig = DIR_PRIVADO . '/config.php';
if (!is_file($archivoConfig)) {
    error_portal(503, 'Página en preparación', 'Estamos terminando de configurar esta página. Inténtelo de nuevo más tarde.');
}
$config = require $archivoConfig;
if (!is_array($config) || empty($config['notion_token']) || strpos((string)$config['notion_token'], 'PEGAR') !== false
    || empty($config['ds_portal_clientes']) || empty($config['ds_portal_hitos'])) {
    error_portal(503, 'Página en preparación', 'Estamos terminando de configurar esta página. Inténtelo de nuevo más tarde.');
}
if (!function_exists('curl_init')) {
    error_portal(503, 'Página no disponible', 'Inténtelo de nuevo más tarde.');
}

$clave = (isset($_GET['c']) && is_string($_GET['c'])) ? trim($_GET['c']) : '';
if (!preg_match('/^[A-Za-z0-9]{16,64}$/', $clave)) {
    error_portal(404, 'Enlace no válido', 'Revise que el enlace esté completo. Si el problema sigue, escríbanos y se lo enviamos de nuevo.');
}

$minutos = max(1, (int)($config['minutos_cache'] ?? 5));
$dirCopia = DIR_PRIVADO . '/cache';
if (!is_dir($dirCopia)) {
    @mkdir($dirCopia, 0755, true);
}
$archivoCopia = $dirCopia . '/' . hash('sha256', $clave) . '.json';

$copia = null;
if (is_file($archivoCopia)) {
    $copia = json_decode((string)@file_get_contents($archivoCopia), true);
    if (is_array($copia) && (time() - (int)($copia['_guardado'] ?? 0)) < $minutos * 60) {
        responder($copia);
    }
}

try {
    $datos = cargar_desde_notion($config, $clave);
    $datos['_guardado'] = time();
    @file_put_contents($archivoCopia, (string)json_encode($datos, JSON_UNESCAPED_UNICODE), LOCK_EX);
    responder($datos);
} catch (Throwable $e) {
    $detalle = $e->getMessage(); // nunca incluye el token
    error_log('Portal Mesa Urbana: ' . $detalle);
    // detalle completo para revisar desde el Administrador de archivos (la carpeta privado/ no se sirve por la web)
    @file_put_contents($dirCopia . '/ultimo-error.txt', gmdate('c') . ' ' . $detalle . PHP_EOL, LOCK_EX);
    if (is_array($copia)) {
        responder($copia); // mejor la última copia que una página en blanco
    }
    // referencia corta para soporte: N401 = token, N404 = la integración no ve las bases, N400 = consulta, R = red
    $referencia = 'G';
    if (preg_match('/Notion HTTP (\d{3})/', $detalle, $m)) {
        $referencia = 'N' . $m[1];
    } elseif (strpos($detalle, 'curl') === 0) {
        $referencia = 'R';
    }
    error_portal(503, 'No pudimos cargar la información', 'Inténtelo de nuevo en unos minutos. Si el problema sigue, escríbanos. (Referencia: ' . $referencia . ')');
}
