<?php

namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route; // Note: 'Attribute' est mieux pour les versions récentes
use Doctrine\DBAL\Connection;

class ApiCheckoutController extends AbstractController
{
    #[Route('/api/checkout', name: 'api_checkout', methods: ['POST'])]
    public function checkout(Request $request, EntityManagerInterface $em, Connection $connection): JsonResponse
    {
        // ... Tout le code que je t'ai donné avant ...
        // Je le remets pour être sûr que tu as tout :
        
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Non connecté'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $panierItems = $data['panier'] ?? [];

        if (empty($panierItems)) {
            return $this->json(['error' => 'Panier vide'], 400);
        }

        // CALCUL DU TOTAL
        $totalCalcul = 0;
        foreach ($panierItems as $item) {
            if (isset($item['prix'])) {
                $totalCalcul += (float)$item['prix'];
            }
        }

        // CRÉATION COMMANDE
        $commande = new Commande();
        $commande->setIdUser($user->getId());
        $commande->setDateCommande(new \DateTime());
        $commande->setTotal($totalCalcul);

        $em->persist($commande);
        $em->flush();

        $refCom = $commande->getId();

        // REMPLISSAGE PANIER
        $quantites = array_count_values(array_column($panierItems, 'id'));

        foreach ($quantites as $idProduit => $qte) {
            $connection->executeStatement(
                'INSERT INTO panier (id_produit, ref_com, qte) VALUES (:id_produit, :ref_com, :qte)',
                [
                    'id_produit' => $idProduit,
                    'ref_com' => $refCom,
                    'qte' => $qte
                ]
            );
        }

        return $this->json([
            'success' => true, 
            'message' => 'Commande validée !',
            'ref_com' => $refCom
        ]);
    }
}