<?php

namespace App\Controller;

use App\Entity\Owner;
use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction')]
class TransactionController extends AbstractController
{
    #[Route('/', name: 'transaction_index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository): Response
    {
        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactionRepository->findAll(),
        ]);
    }

    #[Route('/new/owner/{id}', name: 'transaction_new_owner', methods: ['GET','POST'])]
    public function new(Request $request, Owner $owner): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction, [
            'owner' => $owner
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $debitBalanceCurrent =  $transaction->getDebitAccount()->getBalance();
            $creditBalanceCurrent =  $transaction->getCreditAccount()->getBalance();
            $montant = $transaction->getMontant();
            $resultDebit = $debitBalanceCurrent - $montant;
            $resultCredit = $creditBalanceCurrent + $montant;

            $transaction->setDate(new \DateTime('now'));
            //(new \DateTime('now'));
            $transaction->getDebitAccount()->setBalance($resultDebit);
            $transaction->getCreditAccount()->setBalance($resultCredit);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('owner_show', ['id' => $owner->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/{id}/edit', name: 'transaction_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Transaction $transaction): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {

            $resetBalanceDebit = $transaction->getDebitAccount()->getBalance() + $transaction->getMontant();
            $resetBalanceCredit = $transaction->getCreditAccount()->getBalance() - $transaction->getMontant();

            $transaction->getCreditAccount()->setBalance($resetBalanceCredit);
            $transaction->getDebitAccount()->setBalance($resetBalanceDebit);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
