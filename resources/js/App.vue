<template>
  <div>
    <!-- Vue app root component -->
    <component :is="currentComponent" />
  </div>
</template>

<script>
export default {
  data() {
    return {
      currentComponent: null
    };
  },
  mounted() {
    // Dynamically load components based on the page
    const componentName = document.body.dataset.vueComponent;
    if (componentName) {
      import(`./components/${componentName}.vue`)
        .then(module => {
          this.currentComponent = module.default;
        })
        .catch(error => {
          console.error(`Failed to load component: ${componentName}`, error);
        });
    }
  }
};
</script>
