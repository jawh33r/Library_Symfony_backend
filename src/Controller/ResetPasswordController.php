<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



final class ResetPasswordController extends AbstractController
{
    private $entityManager;
    private $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    #[Route('/forgot-password', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_borrowing_index');
        }

        if ($request->get('email')) {
            $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($request->get('email'));

            if (!$user) {
                $this->addFlash('danger', 'Email not found.');
                return $this->redirectToRoute('reset_password');
            }

            // TEMP: token not stored yet
            $resetToken = bin2hex(random_bytes(32));

            $user->setResetToken($resetToken);
            $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
            $this->entityManager->flush();
            $resetUrl = $this->generateUrl(
                'app_reset_password',   
                ['token' => $resetToken],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $emailMessage = (new Email())
                    ->from('jawh3r@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Reset your password')
                    ->html("
                        <h2>Password Reset Request</h2>
                        <p>Hello {$user->getEmail()},</p>
                        <p>We received a request to reset your password. Click the button below to reset it:</p>
                        <p style='text-align:center;'>
                            <a href='{$resetUrl}' style='background-color:#4CAF50;color:white;padding:12px 20px;
                            text-decoration:none;border-radius:5px;display:inline-block;'>Reset Password</a>
                        </p>
                        <p>If you didn't request this, ignore this email.</p>
                        <p>Thanks,<br>Library System</p>
                    ");
            $this->mailer->send($emailMessage);

            $this->addFlash('success', 'A reset link was sent to your email.');
            return $this->render('reset_password/check_email.html.twig');
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
public function reset(
    string $token,
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher
    ): Response {
    $user = $em->getRepository(User::class)->findOneBy(['resetToken' => $token]);

    if (!$user || $user->getResetTokenExpiresAt() < new \DateTime()) {
        $this->addFlash('danger', 'Invalid or expired reset link.');
        return $this->redirectToRoute('reset_password');
    }

    // Handle form submission
    if ($request->isMethod('POST')) {
        $newPassword = $request->request->get('password');
        $confirmPassword = $request->request->get('password_confirm');

        if ($newPassword !== $confirmPassword) {
            $this->addFlash('danger', 'Passwords do not match.');
        } else {
            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            // Clear the token so it cannot be reused
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);

            $em->flush();

            $this->addFlash('success', 'Password updated successfully. You can now log in.');
            return $this->redirectToRoute('app_borrowing_index'); // make sure this route exists
        }
    }

    return $this->render('reset_password/reset_form.html.twig', [
        'token' => $token
    ]);
    }
}
