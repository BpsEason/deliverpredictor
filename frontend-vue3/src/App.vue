<template>
  <div id="app">
    <header>
      <h1>DeliverPredictor</h1>
      <nav>
        <router-link to="/">儀表板</router-link>
      </nav>
    </header>
    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
nav a {
  margin: 0 1rem;
}
</style>
