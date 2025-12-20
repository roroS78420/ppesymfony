<template>
  <div id="page-wrapper">
    
    <header>
      <div class="header-content">
        <img src="/img/logo.png" alt="Logo PPE" class="logo" @error="imageError"> 
        
        <nav>
          <ul>
            <li><a href="#">Accueil</a></li>
            <li><a href="#">Voir les produits</a></li>
            <li><a href="#">Panier</a></li>
            <li><a href="#">Connexion</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <main class="container">
      <h1>Bienvenue sur la Boutique PPE</h1>

      <div v-if="loading" class="loading">Chargement des produits...</div>

      <div v-else class="produits-grid">
        <div v-for="produit in produits" :key="produit.id" class="produit-item">
          
          <div class="image-container">
             <img :src="getImageUrl(produit)" :alt="produit.nomProduit">
          </div>

          <div class="info-produit">
            <h3>{{ produit.nomProduit }}</h3>
            <p class="desc">{{ produit.description }}</p>
            <div class="prix-action">
              <span class="prix">{{ produit.prix }} €</span>
              <button class="btn-panier">Ajouter</button>
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
      loading: true
    }
  },
  mounted() {
    this.getProduits();
  },
  methods: {
    async getProduits() {
      try {
        // CORRECTIF CRITIQUE : On force le header pour API Platform
        const response = await fetch('/api/produits', {
            headers: {
                'Accept': 'application/ld+json'
            }
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.member) {
            this.produits = data.member;
        } else if (data['hydra:member']) {
            this.produits = data['hydra:member'];
        } else {
            this.produits = data;
        }
        
        this.loading = false;

      } catch (error) {
        console.error("Erreur de chargement :", error);
        this.loading = false;
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
    }
  }
}
</script>

<style scoped>
.header-content { display: flex; align-items: center; justify-content: space-between; padding: 10px; background: #f8f9fa; margin-bottom: 20px;}
.logo { max-height: 50px; }
nav ul { list-style: none; display: flex; gap: 15px; padding: 0; }
nav a { text-decoration: none; color: #333; font-weight: bold; }

.produits-grid { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;}
.produit-item { border: 1px solid #ccc; padding: 15px; width: 250px; border-radius: 5px; background: white; display: flex; flex-direction: column; justify-content: space-between;}
.image-container { text-align: center; margin-bottom: 10px; }
.image-container img { max-width: 100%; height: 150px; object-fit: contain; }
.prix { font-weight: bold; color: green; font-size: 1.2em; }
.prix-action { display: flex; justify-content: space-between; align-items: center; margin-top: 10px;}
.btn-panier { background-color: #007bff; color: white; border: none; padding: 8px 12px; cursor: pointer; border-radius: 4px;}
.btn-panier:hover { background-color: #0056b3; }
</style>