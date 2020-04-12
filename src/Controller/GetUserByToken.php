<?php

namespace IHelpShopping\Controller;

use API\UserBundle\Model\UserManagerInterface;
use IHelpShopping\Dto\UserDataByToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class GetUserByToken extends AbstractController
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route(
     *     name="api_user_get_data_by_token",
     *     path="/api/users/get-data-by-token/{token}",
     *     methods={"GET"},
     *     defaults={
     *          "_api_normalization_context"={"api_sub_level"=true},
     *          "_api_swagger_context"={
     *              "tags"={"User"},
     *              "summary"="Get User data by token.",
     *              "responses"={
     *                  "200"={
     *                      "schema"={
     *                          "properties"={
     *                              "first_name"={"type"="string"},
     *                              "middle_name"={"type"="string"},
     *                              "last_name"={"type"="string"},
     *                              "admin"={"type"="boolean"},
     *                          }
     *                      }
     *                  },
     *                  "404"={
     *                      "description"="User not found.",
     *                  }
     *              }
     *          }
     *     }
     * )
     */
    public function __invoke(string $token)
    {
        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException('Token invalid.');
        }

        return $this->json(new UserDataByToken($user));
    }
}
