<template>
  <main class="main not-found-page">
    <section class="container text-center py-10">
      <h1 class="text-4xl font-bold mb-4">404</h1>
      <p class="mb-6">Sorry, the page you are looking for could not be found.</p>
      <router-link to="/" class="btn btn-primary">Return Home</router-link>
    </section>
  </main>
</template>

<script>
export default {
  name: 'NotFound',
}
</script> 