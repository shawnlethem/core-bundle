<?php
namespace VideoGamesRecords\CoreBundle\EventSubscriber\Badge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VideoGamesRecords\CoreBundle\Event\SerieEvent;
use VideoGamesRecords\CoreBundle\Handler\Badge\PlayerSerieBadgeHandler;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreEvents;

final class UpdatePlayerSerieBadgeSubscriber implements EventSubscriberInterface
{
    private PlayerSerieBadgeHandler $badgeHandler;

    public function __construct(PlayerSerieBadgeHandler $badgeHandler)
    {
        $this->badgeHandler = $badgeHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            VideoGamesRecordsCoreEvents::SERIE_MAJ_COMPLETED => 'process',
        ];
    }

    /**
     * @param SerieEvent $event
     */
    public function process(SerieEvent $event)
    {
        $this->badgeHandler->process($event->getSerie());
    }
}
