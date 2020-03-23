<?php
/**
 * Created by PhpStorm.
 * User: Gianni GIUDICE
 * Date: 06/12/2019
 * Time: 17:50
 */

namespace App\Controller;


use App\Entity\Account;
use App\Form\Type\Manager\DeleteAccountType;
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
    public function edit(Request $request, int $id) {
        $errors = null;
        if ($account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $id, 'owner' => $this->user])) {

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
                    } else {
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
        else {
            return $this->render('authenticated/manager/edit.html.twig', [
                'fatal_error' => 'Ce compte n\'existe pas.'
            ]);
        }
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
     * @Route("/validateTransfer", name="validate_transfer")
     */
    public function validateTransfer(Request $request) {
        // Appel AJAX
        if ($request->isXmlHttpRequest()) {
            $amount = $request->get('amount');
            $toDebit = $request->get('toDebit');
            $toCredit = $request->get('toCredit');

            $accountD =  $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $toDebit, 'owner' => $this->user]);
            $accountC =  $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $toCredit, 'owner' => $this->user]);
            /* Si les deux comptes existent, appartiennent à l'utilisateur et si le compte à débiter possède au moins
            la somme demandée */
            if ($accountD && $accountC && $accountD->getAmount() >= $amount) {
                $accountD->setAmount($accountD->getAmount() - $amount);
                $accountC->setAmount($accountC->getAmount() + $amount);

                $this->em->persist($accountD);
                $this->em->persist($accountC);
                $this->em->flush();

                return $this->render('authenticated/manager/display_transfer_success_msg.html.twig', [
                    'amount' => $amount,
                    'accountD' => $accountD,
                    'accountC' => $accountC
                ]);
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

    /**
     * @Route("/delete/{id}", name="delete_account")
     */
    public function delete(Request $request, int $id) {
        $errors = null;
        if ($account = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['id' => $id, 'owner' => $this->user])) {
            $form = $this->createForm(DeleteAccountType::class, $account);

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $errors = [];
                foreach ($form->getErrors(true, true) as $error) {
                    $propertyPath = str_replace('data.', '', $error->getCause()->getPropertyPath());
                    $errors[$propertyPath] = $error->getMessage();
                }

                if ($form->isValid()) {
                    $this->em->remove($account);
                    $this->em->flush();
                    $flashbag = $this->get('session')->getFlashBag();
                    $flashbag->add('success', 'Le compte a bien été supprimé.');

                    return $this->redirectToRoute('home_manager');
                }

            }
            return $this->render('authenticated/manager/delete.html.twig', [
                'account' => $account,
                'form' => $form->createView(),
                'errors' => $errors
            ]);
        }
        else {
            return $this->render('authenticated/manager/delete.html.twig', [
                'fatal_error' => 'Ce compte n\'existe pas.'
            ]);
        }
    }
}