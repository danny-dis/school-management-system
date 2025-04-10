<template>
  <div class="not-found">
    <div class="error-page">
      <h2 class="headline text-warning">404</h2>
      <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>
        <p>
          We could not find the page you were looking for.
          Meanwhile, you may <router-link to="/">return to dashboard</router-link>.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound'
};
</script>

<style scoped>
.not-found {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 500px;
}

.error-page {
  margin: 20px auto 0;
  text-align: center;
}

.error-page > .headline {
  float: left;
  font-size: 100px;
  font-weight: 300;
}

.error-page > .error-content {
  display: block;
  margin-left: 190px;
  text-align: left;
}

.error-page > .error-content > h3 {
  font-size: 25px;
  font-weight: 300;
  margin-top: 0;
}
</style>
