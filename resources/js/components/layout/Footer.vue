<template>
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Version {{ appVersion }}
    </div>
    <strong>Copyright &copy; {{ currentYear }} <a href="https://zophlic.com">Zophlic</a>.</strong> All rights reserved.
  </footer>
</template>

<script>
export default {
  name: 'FooterComponent',
  data() {
    return {
      appVersion: '1.0.0',
      currentYear: new Date().getFullYear()
    };
  }
};
</script>
