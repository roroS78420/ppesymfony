import { useState, useEffect } from 'react'
import './App.css'

const PRODUITS_DEMO = [
  { id: 1, nomProduit: 'Casque audio', prix: 59.99 },
  { id: 2, nomProduit: 'Clavier mécanique', prix: 89.5 },
  { id: 3, nomProduit: 'Souris sans fil', prix: 24.9 },
]

function App() {
  // On charge intelligemment DÈS la création de l'état
  const [panier, setPanier] = useState(() => {
    const savedCart = localStorage.getItem('monPanierPPE');
    // S'il y a un panier sauvegardé, on le parse, sinon on démarre avec un tableau vide
    return savedCart ? JSON.parse(savedCart) : [];
  });

  // Et on ne garde que l'effet de sauvegarde !
  useEffect(() => {
    localStorage.setItem('monPanierPPE', JSON.stringify(panier));
  }, [panier]);

  // Nouveau state pour la confirmation (faux par défaut)
  const [commandeValidee, setCommandeValidee] = useState(false);

  // Fonction déclenchée par le bouton
  const validerCommande = () => {
    setPanier([]); // Vide le panier en l'écrasant avec un tableau vide
    setCommandeValidee(true); // Passe le booléen à vrai pour afficher le message
  };

  const addToCart = (produit) => {
    // On utilise la valeur la plus récente garantie par React (prevPanier)
    setPanier(prevPanier => [...prevPanier, produit]);
  };

  const removeFromCart = (indexToRemove) => {
    // Pareil ici : si on supprime deux éléments très vite,
    // on s'assure de ne pas ressusciter un élément supprimé par erreur !
    setPanier(prevPanier => prevPanier.filter((_, index) => index !== indexToRemove));
  };

  // 1. Le calcul du total (équivalent d'un computed en Vue,
  // mais ici c'est juste une variable recalculée à chaque rendu)
  const total = panier.reduce((somme, produit) => somme + produit.prix, 0);

  return (
    <div className="app">
      <h1>Boutique</h1>

      {/* Liste des produits */}
      <h2>Produits disponibles</h2>
      <ul>
        {PRODUITS_DEMO.map((produit) => (
          // On suppose que les produits de démo ont un 'id' pour la key
          <li key={produit.id}>
            {produit.nomProduit} - {produit.prix} €
            {/* Attention au onClick : il faut une fonction fléchée () => ...
                Sinon la fonction s'exécute toute seule au moment du rendu ! */}
            <button onClick={() => addToCart(produit)}>
              Ajouter
            </button>
          </li>
        ))}
      </ul>

      {/* Liste du panier */}
      <h2>Mon Panier</h2>
      <ul>
        {panier.map((produit, index) => (
          // Ici on utilise l'index comme key, comme tu l'as souligné pour l'aparté "entretien"
          <li key={index}>
            {produit.nomProduit} - {produit.prix} €
            <button onClick={() => removeFromCart(index)}>
              Supprimer
            </button>
          </li>
        ))}
      </ul>

      {/* Affichage du total */}
      <h3>Total : {total.toFixed(2)} €</h3>

      {panier.length > 0 && (
        <button onClick={validerCommande}>Valider la commande</button>
      )}

      {/* Affichage conditionnel : ne s'affiche que si commandeValidee est vrai */}
      {commandeValidee && <p>Commande validée, merci !</p>}
    </div>
  )
}

export default App
