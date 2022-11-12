<?php

namespace VideoGamesRecords\CoreBundle\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Repository\TeamRepository;

/**
 * Class TeamController
 */
class TeamController extends DefaultController
{
    private TranslatorInterface $translator;
    private TeamRepository $teamRepository;

    private array $extensions = array(
        'image/png' => '.png',
        'image/jpeg' => '.jpg',
    );

    public function __construct(TranslatorInterface $translator, TeamRepository $teamRepository)
    {
        $this->translator = $translator;
        $this->teamRepository = $teamRepository;
    }

    /**
     * @param Request     $request
     * @return Response
     * @throws Exception
     */
    public function uploadAvatar(Request $request): Response
    {
        $team = $this->getTeam();
        $data = json_decode($request->getContent(), true);
        $file = $data['file'];
        $fp1 = fopen($file, 'r');
        $meta = stream_get_meta_data($fp1);

        $data = explode(',', $file);

        if (!array_key_exists($meta['mediatype'], $this->extensions)) {
            return $this->getResponse(false, $this->translator->trans('avatar.extension_not_allowed'));
        }

        $directory = $this->getParameter('videogamesrecords_core.directory.picture') . '/team';
        $filename = $team->getId() . '_' . uniqid() . $this->extensions[$meta['mediatype']];

        $fp2 = fopen($directory . '/' . $filename, 'w');
        fwrite($fp2, base64_decode($data[1]));
        fclose($fp2);
        // Save avatar

        $team->setLogo($filename);

        $this->teamRepository->flush();
        return $this->getResponse(true, $this->translator->trans('avatar.success'));
    }
}