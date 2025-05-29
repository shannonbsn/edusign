<template>
  <div class="auth-container">
    <nav>
      <button @click="$router.push('/auth/login')">Connexion</button>
      <button @click="$router.push('/auth/register')">Inscription</button>
    </nav>
    
    <router-view></router-view>
  </div>
</template>