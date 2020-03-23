<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 05/11/2019
 * Time: 21:20
 */

namespace App\Controller;


use App\Entity\Account;
use App\Form\Type\Manager\AddAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class ManagerController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class ManagerController extends AbstractController {
    private $em;
    private $user;

    public function __construct(EntityManagerInterface $entityManager, Security $security) {
        $this->em = $entityManager;
        $this->user = $security->getUser();
    }

    /**
     * @Route("/manager", name="home_manager")
     */
    public function index(Request $request) {
        $errors = null;

        $account = new Account();
        $form = $this->createForm(AddAccountType::class, $account);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $propertyPath = str_replace('data.', '', $error->getCause()->getPropertyPath());
                $errors[$propertyPath] = $error->getMessage();
            }

            if ($form->isValid()) {
                if ($account->getColor()) {
                    $res = $this->getDoctrine()->getRepository(Account::class)
                        ->findAccountNameWithOwner($account->getTitle(), $this->user);
                    if (!$res) {
                        $account->setOwner($this->user);
                        $this->em->persist($account);
                        $this->em->flush();
                    } else {
                        array_push($errors, 'Vous avez déjà un compte à ce nom.');
                    }
                }
                else {
                    array_push($errors, 'Vous devez choisir une couleur.');
                }
            }
        }

        return $this->render('authenticated/manager/index.html.twig', [
            'user' => $this->user,
            'accounts' => $this->user->getAccounts(),
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }
}