<?php

return [
    require(realpath(__DIR__ . '/../../apps/basic/config/web.php')),
    require(realpath(__DIR__ . '/../../apps/advanced/backend/config/command_routes_collect.php')),
    require(realpath(__DIR__ . '/../../apps/advanced/frontend/config/command_routes_collect.php')),
];
