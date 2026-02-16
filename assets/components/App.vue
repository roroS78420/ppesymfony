<template>
  <div id="page-wrapper">
    
    <header>
      <div class="header-content">
        <img src="/img/logo.png" alt="Logo PPE" class="logo" @error="imageError"> 
        
        <nav>
          <ul>
            <li><a href="#" @click.prevent="pageEncours = 'home'">Accueil</a></li>
            
            <li><a href="#" @click.prevent="pageEncours = 'panier'">Panier ({{ panier.length }})</a></li>

            <li v-if="user" class="user-menu">
                <a href="#" @click.prevent="fetchMesCommandes">Mes Commandes</a>
                <span>Bonjour, {{ user.username }} !</span>
                <a href="/logout" class="btn-logout">Déconnexion</a>
            </li>

            <li v-else>
                <a href="/login">Connexion</a>
            </li>
          </ul>
        </nav>
      </div>
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
                    <th>Montant Total</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="cmd in mesCommandes" :key="cmd.ref_com">
                    <td>#{{ cmd.ref_com }}</td>
                    <td>{{ cmd.date_commande.split(' ')[0] }}</td> 
                    <td style="font-weight:bold; color:green;">{{ cmd.total }} €</td>
                    <td><span class="badge-success">Validée</span></td>
                </tr>
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
             <a href="#" @click.prevent="pageEncours = 'home'">← Retour aux achats</a>
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
      mesCommandes: []
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
  } // Fin methods
}
</script>

<style scoped>
.header-content { display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f8f9fa; margin-bottom: 20px;}
.logo { max-height: 50px; }
nav ul { list-style: none; display: flex; gap: 15px; padding: 0; }
nav a { text-decoration: none; color: #333; font-weight: bold; }
.user-menu { display: flex; gap: 15px; align-items: center; color: #007bff; font-weight: bold; }
.btn-logout { color: red !important; border: 1px solid red; padding: 2px 8px; border-radius: 4px; font-size: 0.9em; }
.btn-logout:hover { background: red; color: white !important; }
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