<?php

use App\Enums\CommentStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PlanTypeEnum;
use App\Enums\UserRoleEnum;
use App\Enums\VoucherTypeEnum;

return VoucherTypeEnum::getLocalizeFaDescription()
        + UserRoleEnum::getLocalizeFaDescription()
        + OrderTypeEnum::getLocalizeFaDescription()
        + CommentStatusEnum::getLocalizeFaDescription()
        + PlanTypeEnum::getLocalizeFaDescription();
