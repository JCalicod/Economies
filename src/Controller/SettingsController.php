<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 11/11/2019
 * Time: 18:41
 */

namespace App\Controller;


use App\Entity\User;
use App\Form\Type\Manager\SettingsType;
use App\Services\SettingsServices;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class SettingsController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */

class SettingsController extends AbstractController {
    private $em;
    private $user;
    private $encoder;
    private $settingsServices;

    public function __construct(EntityManagerInterface $entityManager, Security $security, UserPasswordEncoderInterface $passwordEncoder, SettingsServices $settingServices) {
        $this->em = $entityManager;
        $user = $security->getUser();
        $this->user = $this->em->getRepository(User::class)->findOneBy(['email' => $user->getUsername()]);
        $this->encoder = $passwordEncoder;
        $this->settingsServices = $settingServices;
    }

    /**
     * @Route("/settings", name="settings")
     */
    public function index(Request $request) {
        $errors = null;

        $form = $this->createForm(SettingsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = $this->settingsServices->getErrors($form);

            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->encoder->isPasswordValid($this->user, $data['old_password'])) {
                    $this->settingsServices->updateUserSettings($data, $this->user);

                    $this->addFlash('success', 'Vos paramètres ont bien été modifiés.');
                }
                else {
                    array_push($errors, 'Ancien mot de passe incorrect.');
                }

            }
        }

        return $this->render('authenticated/settings/index.html.twig', [
            'form' => $form->createView(),
            'user' => $this->user,
            'errors' => $errors
        ]);
    }
}