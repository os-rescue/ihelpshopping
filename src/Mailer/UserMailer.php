<?php

namespace IHelpShopping\Mailer;

use API\UserBundle\Mailer\EmailTemplateRendererInterface;
use API\UserBundle\Mailer\MailerInterface;
use API\UserBundle\Model\UserInterface;
use API\UserBundle\Mailer\Mailer as BaseMailer;
use Eqsgroup\Enum\EmailAddress;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @final
 */
class UserMailer implements MailerInterface
{
    public const ROUTE_PATH_GET_USER_DATA_BY_TOKEN = 'api_user_get_data_by_token';

    private $mailer;
    private $swiftMailer;
    private $renderer;
    private $router;
    private $parameters;

    public function __construct(
        MailerInterface $mailer,
        \Swift_Mailer $swiftMailer,
        EmailTemplateRendererInterface $renderer,
        UrlGeneratorInterface $router,
        array $parameters
    ) {
        $this->mailer = $mailer;
        $this->swiftMailer = $swiftMailer;
        $this->renderer = $renderer;
        $this->router =$router;
        $this->parameters = $parameters;
    }

    public function sendEmailCreatingConfirmationEmailMessage(UserInterface $user): int
    {
        return $this->mailer->sendEmailCreatingConfirmationEmailMessage($user);
    }

    public function sendEmailUpdatingConfirmationEmailMessage(UserInterface $user): int
    {
        return $this->mailer->sendEmailUpdatingConfirmationEmailMessage($user);
    }

    public function sendPasswordChangingEmailMessage(UserInterface $user): int
    {
        return $this->mailer->sendPasswordChangingEmailMessage($user);
    }

    public function sendPasswordSettingEmailMessage(UserInterface $user): int
    {
        return $this->mailer->sendPasswordSettingEmailMessage($user);
    }

    public function sendPasswordResettingEmailMessage(UserInterface $user): int
    {
        $redirectUrl = $this->router->generate(
            BaseMailer::ROUTE_PATH_RESET_PASSWORD,
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_PATH
        );

        $template = $this->renderer->render(
            $user,
            $this->parameters['password.resetting.template'],
            self::ROUTE_PATH_GET_USER_DATA_BY_TOKEN,
            ['redirectTo' => $redirectUrl]
        );

        return $this->sendEmailMessage($user, $template, $this->parameters['from_email']['password']);
    }

    public function sendRolePromotingEmailMessage(UserInterface $user): int
    {
        return $this->mailer->sendRolePromotingEmailMessage($user);
    }

    private function sendEmailMessage(UserInterface $user, string $renderedTemplate, $fromEmail): int
    {
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = \array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($user->getEmail(), (string) $user)
            ->setBody($body, 'text/html');

        return $this->swiftMailer->send($message);
    }
}
