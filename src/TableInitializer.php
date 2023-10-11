<?php

namespace Palax;

class TableInitializer
{
    public static function showEnd() {?>
        </body>
        </html>
    <?php
    }

    public static function showHead($url = "") {
    ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <title>Финансовый отчет</title>
            <!-- JQuery -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

            <!-- Bootstrap -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

            <!-- Select2 -->
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

            <!-- Chartist -->
            <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
            <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>

            <link rel="stylesheet" href="<?=App::getConfig(self::class)['prefix']?>/public/style.css?v=<?=strtotime('now')?>">
            <script src="//api.bitrix24.com/api/v1/"></script>
        </head>
        <body>
            <header>
                <div class="container">
                    <ul class="nav nav-pills nav-fill">
                        <?php
                        foreach (self::getRoutes() as $route) { ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $route['isCurrent'] ? 'active' : '' ?>" href="<?= $route['route'] ?>"><?= $route['label'] ?></a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </header>
    <?php
    }

    public static function getRoutes(): array
    {
        $config = App::getConfig(self::class);

        return array_map(
            static fn($route) => [
                'route' => $config['prefix'] . $route['route'],
                'isCurrent' => strtok($_SERVER["REQUEST_URI"],'?') === $config['prefix'] . $route['route'],
                'label' => $route['label'],
            ],
            $config['routes']
        );

    }
}