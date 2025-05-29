<template>
  <div class="register-container">
    <h2>Inscription</h2>
    <form @submit.prevent="handleRegister">
      <input v-model="nom" type="text" placeholder="Nom" required>
      <input v-model="prenom" type="text" placeholder="Prénom" required>
      <input v-model="email" type="email" placeholder="Email" required>
      <input v-model="password" type="password" placeholder="Mot de passe" required>

      <select v-model="role" required>
        <option value="admin">Admin</option>
        <option value="intervenant">Intervenant</option>
        <option value="etudiant">Étudiant</option>
      </select>

      <select v-model="classe_id">
        <option value="">Aucune classe</option>
        <option v-for="classe in classes" :key="classe.id" :value="classe.id">
          {{ classe.nom }}
        </option>
      </select>

      <button type="submit">S'inscrire</button>
    </form>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      nom: '',
      prenom: '',
      email: '',
      password: '',
      role: '',
      classe_id: '',
      classes: [],
      errorMessage: ''
    };
  },
  async mounted() {
    try {
      const response = await axios.get('http://localhost:8000/api/classes');
      this.classes = response.data;
    } catch (error) {
      console.error("Erreur lors de la récupération des classes", error);
    }
  },
  methods: {
    async handleRegister() {
      try {
        await axios.post('http://localhost:8000/api/register', {
          nom: this.nom,
          prenom: this.prenom,
          email: this.email,
          password: this.password,
          role: this.role,
          classe_id: this.classe_id ? this.classe_id : null
        });

        this.$router.push('/login');
      } catch (error) {
        this.errorMessage = "Erreur lors de l'inscription.";
      }
    }
  }
};
</script>

<style>
.register-container {
  text-align: center;
}
.error {
  color: red;
  font-weight: bold;
}
</style>