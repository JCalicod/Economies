<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 06/12/2019
 * Time: 17:50
 */

namespace App\Controller;


use App\Entity\Account;
use App\Form\Type\Manager\EditAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AccountManager extends AbstractController {
    private $em;
    private $user;

    public function __construct(EntityManagerInterface $entityManager, Security $security) {
        $this->em = $entityManager;
        $this->user = $security->getUser();
    }

    /**
     * @Route("/account/{id}", name="edit_account")
     */
    public function edit(Request $request, Account $account) {
        $errors = null;

        $form = $this->createForm(EditAccountType::class, $account);

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
                        ->findAnotherAccountNameWithOwner($account, $this->user);
                    if (!$res) {
                        $this->em->persist($account);
                        $this->em->flush();
                        $this->addFlash('success', 'Le compte a bien été modifié.');
                    } else {
                        array_push($errors, 'Vous avez déjà un compte à ce nom.');
                    }
                }
                else {
                    array_push($errors, 'Vous devez choisir une couleur.');
                }
            }
        }
        return $this->render('authenticated/manager/edit.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/getAccount", name="get_account")
     */
    public function getAccount(Request $request) {
        // Appel AJAX
        if ($request->isXmlHttpRequest()) {
            $accountID = $request->get('accountID');
            $type = $request->get('type');
            $account =  $this->getDoctrine()->getRepository(Account::class)->findOneBy([
                    'id' => $accountID,
                    'owner' => $this->user
                ]);
            // Si le compte existe et appartient à l'utilisateur
            if ($account) {
                if ($type == 'debit') {
                    $other_accounts = $this->getDoctrine()->getRepository(Account::class)->findAllOtherAccounts($accountID, $this->user);

                    return $this->render('authenticated/manager/display_to_debit_account.html.twig', [
                        'account' => $account,
                        'other_accounts' => $other_accounts
                    ]);
                }
                else if ($type == 'credit') {
                    return $this->render('authenticated/manager/display_to_credit_account.html.twig', [
                        'account' => $account
                    ]);
                }
            }
        }
        return $this->redirectToRoute('transfer');
    }

    /**
     * @Route("/transfer", name="transfer")
     */
    public function transfer(Request $request) {
        return $this->render('authenticated/manager/transfer.html.twig', [
            'accounts' => $this->user->getAccounts()
        ]);
    }
}