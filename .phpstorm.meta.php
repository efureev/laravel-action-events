<?php
// @formatter:off

namespace PHPSTORM_META {

    use Fureev\ActionEvents\ActionLogger;
    use Sitesoft\Alice\Modules\News\Services\NewsService;
    use Sitesoft\Alice\Sites\Entity\CurrentSite;

    override(
        \app(0),
        map(
            [
                'Fureev\ActionEvents\ActionLogger' => ActionLogger::class,
                'ActionLogger'                     => ActionLogger::class,
            ]
        )
    );
}
