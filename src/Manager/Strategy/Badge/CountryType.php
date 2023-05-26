<?php

namespace VideoGamesRecords\CoreBundle\Manager\Strategy\Badge;

use App\Manager\Strategy\AbstractCompanyRuleStrategy;
use VideoGamesRecords\CoreBundle\Contracts\Strategy\BadgeTypeStrategyInterface;
use VideoGamesRecords\CoreBundle\Entity\Badge;

class CountryType extends AbstractBadgeStrategy implements BadgeTypeStrategyInterface
{
    /**
     * @param Badge $badge
     * @return bool
     */
    public function supports(Badge $badge): bool
    {
        return $badge->getType() === self::TYPE_VGR_SPECIAL_COUNTRY;
    }

    /**
     * @param Badge $badge
     * @return string
     */
    public function getTitle(Badge $badge): string
    {
        return $badge->getCountry()->getName();
    }
}
