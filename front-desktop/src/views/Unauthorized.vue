<template>
  <div class="unauthorized-container">
    <h2>🚫 Accès refusé</h2>
    <p>Vous n’avez pas les permissions nécessaires pour accéder à cette page.</p>
    <button @click="$router.push('/auth/login')">🔑 Retour à la connexion</button>
  </div>
</template>

<style>
.unauthorized-container {
  text-align: center;
  margin-top: 50px;
}

button {
  background-color: #ff4d4d;
  color: white;
  padding: 10px 15px;
  border: none;
  cursor: pointer;
  border-radius: 5px;
}

button:hover {
  background-color: #cc0000;
}
</style>