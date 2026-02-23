<?php

function is_mobile_device(): bool
{
    $agent = service('request')->getUserAgent();
    return $agent->isMobile();
}
