<?php

require_once dirname(__DIR__) . '/src/afribapay.sdk.php';

if (!defined('APP_WEBCHECKOUT_URL')) {
    define('APP_WEBCHECKOUT_URL', sdk_afp_endpoint('checkout') . '/');
}

if (!function_exists('sdk_afp_normalize_path')) {
    function sdk_afp_normalize_path(string $path): string {
        return rtrim(str_replace('\\', '/', $path), '/');
    }
}

if (!function_exists('sdk_afp_url_path_for_dir')) {
    // Chemin URL d'un dossier du disque, calculé depuis le script courant (null si impossible)
    function sdk_afp_url_path_for_dir(string $absDir): ?string {
        $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        if ($scriptFile === '' || $scriptName === '') {
            return null;
        }

        $real = function (string $p): string {
            return sdk_afp_normalize_path(realpath($p) ?: $p);
        };
        $fsFrom = explode('/', $real(dirname(sdk_afp_normalize_path($scriptFile))));
        $fsTo   = explode('/', $real($absDir));

        // Insensible à la casse sous Windows (C:/ vs c:/)
        $isWindows = DIRECTORY_SEPARATOR === '\\' || preg_match('#^[A-Za-z]:/#', $fsTo[0] . '/');
        $i = 0;
        while (isset($fsFrom[$i], $fsTo[$i])
            && ($isWindows ? strcasecmp($fsFrom[$i], $fsTo[$i]) === 0 : $fsFrom[$i] === $fsTo[$i])) {
            $i++;
        }
        if ($i === 0) {
            return null;
        }

        $urlParts = array_values(array_filter(
            explode('/', str_replace('\\', '/', dirname(str_replace('\\', '/', $scriptName)))),
            'strlen'
        ));

        $levelsUp = count($fsFrom) - $i;
        if ($levelsUp > count($urlParts)) {
            return null; // le dossier cible est hors de la racine web
        }

        $urlParts = array_merge(
            array_slice($urlParts, 0, count($urlParts) - $levelsUp),
            array_slice($fsTo, $i)
        );

        return $urlParts ? '/' . implode('/', $urlParts) . '/' : '/';
    }
}

function getExampleHeader($title, $description = '', $showNavigation = true) {
    return '

    <nav class="navbar navbar-light bg-white border-bottom">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="'.APP_WEBCHECKOUT_URL.'" class="navbar-brand d-flex align-items-center">
                    <img src="'.cdn_url("assets/img/logos/afribapay.png").'" 
                         alt="AfribaPay" 
                         class="navbar-brand-img"
                         style="max-width:190px;height:auto;" >
                </a>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-primary">SDK Examples</span>
                <span class="mx-2 text-muted">|</span>
                <a href="https://docs.afribapay.com"  target="_blank" class="text-decoration-none text-primary fw-semibold small">
                    <i class="bi bi-book"></i> Documentation
                </a>
            </div>
        </div>
    </nav>
    <div class="container my-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="border-bottom pb-3 mb-4">
                    <h1 class="h3 mb-2">' . htmlspecialchars($title) . '</h1>
                    ' . ($description ? '<p class="text-muted mb-0">' . htmlspecialchars($description) . '</p>' : '') . '
                </div>
                ' . ($showNavigation ? '
                <nav class="bg-light p-2 rounded mb-4">
                    <a href="'.htmlspecialchars(getExampleRelativePath()).'/index.php" class="text-decoration-none text-primary fw-semibold small">
                        <i class="bi bi-house"></i> Examples Home
                    </a>
                    <span class="mx-2 text-muted">|</span>
                    <a href="'.APP_WEBCHECKOUT_URL.'ck/examples/sdk" target="_blank" class="text-decoration-none text-primary fw-semibold small">
                        <i class="bi bi-globe"></i> Online Demo
                    </a>
                    <span class="mx-2 text-muted">|</span>
                    <a href="https://docs.afribapay.com" target="_blank" class="text-decoration-none text-primary fw-semibold small">
                        <i class="bi bi-book"></i> Documentation
                    </a>
                </nav>' : '');
}

function getExampleFooter() {
    return '
            </div>
        </div>
        <footer class="text-center text-muted small mt-4 py-3">
            <p class="mb-1">
                <i class="bi bi-info-circle"></i> 
                For more information, visit 
                <a href="https://docs.afribapay.com" target="_blank" class="text-decoration-none">docs.afribapay.com</a>
            </p>
        </footer>
    </div>
';
}

function cdn_url($path) {
    return sdk_afp_endpoint('cdn') . '/' . ltrim($path, '/');
}

function getDocumentHead($title = 'Web Checkout') {
    return '<!DOCTYPE html>
              <html data-bs-theme="light" lang="en-US" dir="ltr">
                <head>
                  <meta charset="utf-8">
                  <meta http-equiv="X-UA-Compatible" content="IE=edge">
                  <meta name="viewport" content="width=device-width, initial-scale=1">
                  <title>' . htmlspecialchars($title) . ' | AfribaPay</title>
                  <meta name="theme-color" content="#ffffff">
                  <script src="' . cdn_url('assets/js/config.js') . '" defer></script>
                  <script src="' . cdn_url('assets/vendors/simplebar/simplebar.min.js') . '" defer></script>
                  <link rel="preconnect" href="https://fonts.gstatic.com">
                  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
                  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
                  <link href="' . cdn_url('assets/vendors/simplebar/simplebar.min.css') . '" rel="stylesheet">
                  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
                  <link href="' . cdn_url('assets/css/theme.min.css') . '" rel="stylesheet" id="style-default">
                  <link href="' . cdn_url('assets/css/user.css') . '" rel="stylesheet" id="user-style-default">
                </head>
                <body>
                  <main class="main" id="top">
                    <div class="container-fluid p-0" data-layout="container-fluid">
                      <div class="content">
';
}

function getDocumentFooter($version = 'v0.2') {
    return '
              <footer class="footer">
                <div class="container">
                  <div class="row g-0 justify-content-between fs-10 mt-4 mb-3">
                    <div class="col-12 col-sm-auto text-center">
                      <p class="mb-0 text-600">&copy; 2023 - ' . date('Y') . ' <strong><span>AfribaPAY</span></strong>. All Rights Reserved.</p>
                    </div>
                    <div class="col-12 col-sm-auto text-center">
                      <p class="mb-0 text-600">' . htmlspecialchars($version) . '</p>
                    </div>
                  </div>
                </div>
              </footer>
            </div>
          </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
      </body>
    </html>
';
}

function getExampleRelativePath(): string {
  return rtrim(sdk_afp_url_path_for_dir(__DIR__) ?? '/examples/', '/');
}
