<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route; // <--- J'ai mis 'Attribute' au lieu de 'Annotation'
use Doctrine\DBAL\Connection;

class ApiAccountController extends AbstractController
{
    #[Route('/api/mes-commandes', name: 'api_mes_commandes', methods: ['GET'])]
    public function getMesCommandes(Connection $connection): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Non connecté'], 401);
        }

        // On récupère toutes les commandes de l'utilisateur (id_u)
        // On trie par date décroissante (la plus récente en haut)
        $sql = "SELECT * FROM commande WHERE id_u = :id_user ORDER BY date_commande DESC";
        
        $commandes = $connection->fetchAllAssociative($sql, [
            'id_user' => $user->getId()
        ]);

        return $this->json($commandes);
    }
}