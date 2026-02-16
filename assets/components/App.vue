<template>
  <div id="page-wrapper">
    
<header>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
      
      <a class="navbar-brand fw-bold text-primary" href="#" @click.prevent="pageEncours = 'home'" style="font-size: 1.5rem;">
        ⚡ Boutique PPE
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
          
          <li class="nav-item me-3">
            <a class="nav-link" href="#" @click.prevent="pageEncours = 'home'" :class="{ active: pageEncours === 'home' }">
              🏠 Accueil
            </a>
          </li>
          
          <li class="nav-item me-3">
            <a class="nav-link position-relative" href="#" @click.prevent="pageEncours = 'panier'" :class="{ active: pageEncours === 'panier' }">
              🛒 Panier
              <span v-if="panier.length > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ panier.length }}
              </span>
            </a>
          </li>

          <li v-if="user" class="nav-item dropdown">
            <a class="nav-link dropdown-toggle fw-bold text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              👤 {{ user.username }}
            </a>
            
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <li><h6 class="dropdown-header">Mon Compte</h6></li>
              
            <li>
                <a class="dropdown-item" href="#" @click.prevent="pageEncours = 'settings'">
                    ⚙️ Mes Paramètres
                </a>
            </li>              
              <li>
                <a class="dropdown-item" href="#" @click.prevent="fetchMesCommandes">
                  📦 Mes Commandes
                </a>
              </li>
              
              <li><hr class="dropdown-divider"></li>
              
              <li>
                <a class="dropdown-item text-danger" href="/logout">
                  🚪 Déconnexion
                </a>
              </li>
            </ul>
          </li>

          <li v-else class="nav-item">
            <a href="/login" class="btn btn-primary btn-sm ms-2">
              Connexion
            </a>
          </li>

        </ul>
      </div>
    </div>
  </nav>
  
  <div style="height: 80px;"></div>
</header>

<main class="container">
    <h1>Bienvenue sur la Boutique PPE</h1>

    <div v-if="pageEncours === 'home'">
      <div class="search-bar">
            <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Rechercher un produit (ex: Audi, Volant...)"
                class="form-search"
            >
        </div>
        <div v-if="loading" class="loading">Chargement des produits...</div>
        
        <div v-else class="produits-grid">
            <div v-for="produit in produitsFiltres" :key="produit.id" class="produit-item">
                <div class="image-container">
                     <img :src="getImageUrl(produit)" :alt="produit.nomProduit">
                 </div>
                 <div class="info-produit">
                    <h3>{{ produit.nomProduit }}</h3>
                    <p class="desc">{{ produit.description }}</p>
                    <div class="prix-action">
                      <span class="prix">{{ produit.prix }} €</span>
                      <button class="btn-panier" @click="addToCart(produit)">Ajouter</button>
                    </div>
                 </div>
                 </div>
        </div>
    </div>

    <div v-if="pageEncours === 'panier'" class="vue-panier">
        <h2>Votre Panier</h2>
        
        <div v-if="panier.length === 0" class="panier-vide">
            Votre panier est vide. <a href="#" @click.prevent="pageEncours = 'home'">Retourner aux achats</a>
        </div>

        <div v-else>
            <table class="table-panier">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in panier" :key="index">
                        <td class="col-produit">
                            <img :src="getImageUrl(item)" class="mini-img">
                            <span>{{ item.nomProduit }}</span>
                        </td>
                        <td>{{ item.prix }} €</td>
                        <td>
                            <button class="btn-remove" @click="removeFromCart(index)">Supprimer</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="total-panier">
                <h3>Total : {{ totalPanier }} €</h3>
                <button class="btn-valider" @click="validateOrder">Valider la commande</button>
            </div>
        </div>
    </div>

<div v-if="pageEncours === 'commandes'" class="vue-commandes">
        <h2>Mes Commandes Passées</h2>
        
        <div v-if="mesCommandes.length === 0">
            Aucune commande pour le moment.
            <a href="#" @click.prevent="pageEncours = 'home'">Retourner à la boutique</a>
        </div>

        <table v-else class="table-panier">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            
            <tbody>
                <template v-for="commande in mesCommandes" :key="commande.ref_com">
                    
                    <tr>
                        <td>#{{ commande.ref_com }}</td>
                        <td>{{ commande.date_commande ? commande.date_commande.split(' ')[0] : 'N/A' }}</td>
                        <td style="font-weight:bold; color:green;">{{ commande.total }} €</td>
                        <td>
                            <button @click="voirDetails(commande.ref_com)" class="btn-panier" style="background-color: #17a2b8;">
                                {{ commandeSelectionnee == commande.ref_com ? 'Fermer' : 'Détails' }}
                            </button>
                        </td>
                    </tr>

                    <tr v-if="commandeSelectionnee == commande.ref_com">
                        <td colspan="4" style="background-color: #f8f9fa; padding: 15px;">
                            
                            <div v-if="detailsCommande.length == 0">Chargement...</div>
                            
                            <div v-else>
                                <ul style="list-style: none; padding: 0;">
                                    <li v-for="(item, index) in detailsCommande" :key="index" 
                                        style="border-bottom: 1px solid #ddd; padding: 5px 0; display:flex; justify-content:space-between;">
                                        <span><strong>{{ item.nom }}</strong> (x{{ item.quantite }})</span>
                                        <span>{{ item.prix }} €</span>
                                    </li>
                                </ul>
                            </div>

                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
             <a href="#" @click.prevent="pageEncours = 'home'">← Retour aux achats</a>
        </div>
    </div>
<div v-if="pageEncours === 'settings'" class="container mt-4">
        <h2 class="mb-4 text-center">Bienvenue sur votre profil, {{ user.username }} !</h2>

        <div class="row">
            
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white font-weight-bold">
                        ⚙️ Paramètres du compte
                    </div>
                    <div class="list-group list-group-flush">
                        <button class="list-group-item list-group-item-action" 
                                :class="{ active: activeSettingsTab === 'general' }"
                                @click="activeSettingsTab = 'general'">
                            👤 Informations générales
                        </button>
                        <button class="list-group-item list-group-item-action"
                                :class="{ active: activeSettingsTab === 'pseudo' }"
                                @click="activeSettingsTab = 'pseudo'">
                            📝 Modifier mon Pseudo
                        </button>
                        <button class="list-group-item list-group-item-action"
                                :class="{ active: activeSettingsTab === 'email' }"
                                @click="activeSettingsTab = 'email'">
                            ✉️ Modifier mon email
                        </button>
                        <button class="list-group-item list-group-item-action"
                                :class="{ active: activeSettingsTab === 'password' }"
                                @click="activeSettingsTab = 'password'">
                            🔒 Modifier mon mot de passe
                        </button>
                        <button class="list-group-item list-group-item-action"
                                :class="{ active: activeSettingsTab === 'address' }"
                                @click="activeSettingsTab = 'address'">
                            📍 Modifier mon Adresse
                        </button>
                        <button class="list-group-item list-group-item-action text-danger"
                                :class="{ active: activeSettingsTab === 'delete' }"
                                @click="activeSettingsTab = 'delete'"
                                style="background-color: #fde8e8;">
                            🗑️ Supprimer mon compte
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    
                    <div v-if="activeSettingsTab === 'general'">
                        <div class="card-header bg-white font-weight-bold">Aperçu du profil</div>
                        <div class="card-body">
                            <p><strong>Pseudo :</strong> {{ user.username }}</p>
                            <p><strong>Email :</strong> {{ user.email || 'Non renseigné' }}</p>
                            <hr>
                            <a href="/logout" class="btn btn-secondary">Se Déconnecter</a>
                        </div>
                    </div>

                    <div v-if="activeSettingsTab === 'pseudo'">
                        <div class="card-header bg-white font-weight-bold">Changer de nom d'utilisateur</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nouveau pseudo</label>
                                <input type="text" class="form-control" placeholder="Entrez votre nouveau pseudo">
                            </div>
                            <button class="btn btn-primary">Enregistrer</button>
                        </div>
                    </div>

                    <div v-if="activeSettingsTab === 'email'">
                        <div class="card-header bg-white font-weight-bold">Changer d'adresse e-mail</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nouvel email</label>
                                <input type="email" class="form-control" placeholder="nouveau@email.com">
                            </div>
                            <button class="btn btn-primary">Enregistrer</button>
                        </div>
                    </div>

                    <div v-if="activeSettingsTab === 'password'">
                        <div class="card-header bg-white font-weight-bold">Sécurité</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmer le nouveau mot de passe</label>
                                <input type="password" class="form-control">
                            </div>
                            <button class="btn btn-warning">Mettre à jour le mot de passe</button>
                        </div>
                    </div>

                    <div v-if="activeSettingsTab === 'address'">
                        <div class="card-header bg-white font-weight-bold">Adresse de livraison</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Votre adresse complète</label>
                                <textarea class="form-control" rows="3" placeholder="12 rue de la Paix, 75000 Paris"></textarea>
                            </div>
                            <button class="btn btn-primary">Sauvegarder l'adresse</button>
                        </div>
                    </div>

                    <div v-if="activeSettingsTab === 'delete'">
                        <div class="card-header bg-danger text-white font-weight-bold">Zone de danger</div>
                        <div class="card-body text-center">
                            <h5 class="text-danger">Êtes-vous sûr ?</h5>
                            <p>Cette action est irréversible. Toutes vos commandes et données seront effacées.</p>
                            <button class="btn btn-outline-danger">Oui, supprimer mon compte définitivement</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

    <footer>
      <p>&copy; 2025 - Projet PPE</p>
    </footer>

  </div>
</template>

<script>
export default {
  name: "App",
  data() {
    return {
      produits: [],
      loading: true,
      user: null,
      panier: [],
      pageEncours: 'home',
      searchQuery: '',
      mesCommandes: [],
      detailsCommande: [],
      commandeSelectionnee: null,
      activeSettingsTab: 'general'
    }
  },
  mounted() {
    this.checkUser();
    this.getProduits();
    this.loadCart();
  },
  computed: {
    totalPanier() {
        return this.panier.reduce((total, item) => total + item.prix, 0).toFixed(2);
    },
    produitsFiltres() {
        if (this.searchQuery === '') {
            return this.produits;
        }
        const recherche = this.searchQuery.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

        return this.produits.filter(produit => {
            const nom = produit.nomProduit.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            const desc = produit.description ? produit.description.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "") : "";
            return nom.includes(recherche) || desc.includes(recherche);
        });
    }
  },
  methods: {
    checkUser() {
      const appElement = document.getElementById('app');
      if (appElement && appElement.dataset.user) {
        this.user = JSON.parse(appElement.dataset.user);
      }
    },
    async getProduits() {
      try {
        const response = await fetch('/api/produits', {
            headers: { 'Accept': 'application/ld+json' }
        });
        if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
        const data = await response.json();
        
        if (data.member) this.produits = data.member;
        else if (data['hydra:member']) this.produits = data['hydra:member'];
        else this.produits = data;
        
        this.loading = false;
      } catch (error) {
        console.error("Erreur de chargement :", error);
        this.loading = false;
      }
    },
    addToCart(produit) {
      this.panier.push(produit);
      this.saveCart();
      alert(produit.nomProduit + " ajouté au panier !");
    },
    removeFromCart(index) {
        this.panier.splice(index, 1);
        this.saveCart();
    },
    saveCart() {
      localStorage.setItem('monPanierPPE', JSON.stringify(this.panier));
    },
    loadCart() {
      const savedCart = localStorage.getItem('monPanierPPE');
      if (savedCart) {
        this.panier = JSON.parse(savedCart);
      }
    },
    getImageUrl(produit) {
      if (produit.image && produit.image.nomImage) {
        return '/img/' + produit.image.nomImage;
      }
      return '/img/default.jpg';
    },
    imageError(e) {
      e.target.style.display = 'none'; 
    },
    async validateOrder() {
        if (!this.user) {
            alert("Veuillez vous connecter pour commander.");
            window.location.href = '/login';
            return;
        }
        if (this.panier.length === 0) {
            alert("Votre panier est vide.");
            return;
        }
        if (!confirm("Confirmer la commande de " + this.totalPanier + " € ?")) {
            return;
        }

        try {
            const response = await fetch('/api/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ panier: this.panier })
            });
            const result = await response.json();

            if (response.ok) {
                alert("Commande validée avec succès ! Numéro : " + result.ref_com);
                this.panier = [];
                this.saveCart(); 
                this.pageEncours = 'home';
            } else {
                alert("Erreur : " + result.error);
            }
        } catch (error) {
            console.error("Erreur commande :", error);
            alert("Une erreur est survenue.");
        }
    }, // <--- ICI : Il faut fermer validateOrder avec une accolade et une virgule
    
    // Et fetchMesCommandes doit être en dehors
async fetchMesCommandes() {
        console.log("Tentative de récupération des commandes..."); // Pour voir si le clic marche

        if (!this.user) {
            alert("Utilisateur non connecté !");
            return;
        }

        try {
            const response = await fetch('/api/mes-commandes');
            
            // On vérifie le statut HTTP (200 = OK, 404 = Introuvable, 500 = Erreur Serveur)
            if (response.ok) {
                this.mesCommandes = await response.json();
                console.log("Commandes reçues :", this.mesCommandes);
                this.pageEncours = 'commandes'; // C'est ici que la page change
            } else {
                // SI ON ARRIVE ICI : C'est que Symfony a renvoyé une erreur
                const errorText = await response.text();
                console.error("Erreur API:", errorText);
                alert("Erreur lors de la récupération des commandes (Erreur " + response.status + ")");
            }

        } catch (error) {
            console.error("Erreur JS :", error);
            alert("Impossible de contacter le serveur.");
        }
    },
    async voirDetails(id) {
    const idCommande = Number(id); // On s'assure que c'est un chiffre (ex: 16)
    
    // 1. Si on clique sur la commande déjà ouverte, on ferme tout
    if (this.commandeSelectionnee === idCommande) {
        console.log("Fermeture du détail");
        this.commandeSelectionnee = null;
        this.detailsCommande = [];
        return; 
    }

    // 2. Sinon, on ouvre et on affiche "Chargement..."
    console.log("Ouverture de la commande #" + idCommande);
    this.commandeSelectionnee = idCommande;
    this.detailsCommande = []; 

    try {
        const response = await fetch(`/api/commande-details/${idCommande}`);
        const data = await response.json();

        if (response.ok) {
            console.log("Produits reçus :", data);
            this.detailsCommande = data;
        } else {
            alert("Erreur : " + (data.message || "Erreur inconnue"));
            this.commandeSelectionnee = null;
        }
    } catch (error) {
        console.error("Erreur JS :", error);
        this.commandeSelectionnee = null;
    }
}
  } // Fin methods
}
</script>

<style scoped>
.produits-grid { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;}
.produit-item { border: 1px solid #ccc; padding: 15px; width: 250px; border-radius: 5px; background: white; display: flex; flex-direction: column; justify-content: space-between;}
.image-container { text-align: center; margin-bottom: 10px; }
.image-container img { max-width: 100%; height: 150px; object-fit: contain; }
.prix { font-weight: bold; color: green; font-size: 1.2em; }
.prix-action { display: flex; justify-content: space-between; align-items: center; margin-top: 10px;}

.btn-panier { background-color: #007bff; color: white; border: none; padding: 8px 12px; cursor: pointer; border-radius: 4px;}
.btn-panier:hover { background-color: #0056b3; }

.vue-panier { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.table-panier { width: 100%; border-collapse: collapse; margin-top: 20px; }
.table-panier th, .table-panier td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
.mini-img { width: 50px; height: 50px; object-fit: contain; vertical-align: middle; margin-right: 10px; }
.btn-remove { background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
.total-panier { text-align: right; margin-top: 20px; font-size: 1.2em; }
.btn-valider { background: #28a745; color: white; border: none; padding: 10px 20px; font-size: 1em; border-radius: 5px; cursor: pointer; margin-top: 10px;}

.search-bar { width: 100%; margin-bottom: 20px; text-align: center; }
.form-search { 
    width: 60%; 
    padding: 10px; 
    font-size: 1.1em; 
    border: 1px solid #ddd; 
    border-radius: 20px; 
    outline: none; 
    transition: 0.3s;
}
.form-search:focus { border-color: #007bff; box-shadow: 0 0 5px rgba(0,123,255,0.3); }
.no-result { width: 100%; text-align: center; font-style: italic; color: #666; margin-top: 20px; }
</style>