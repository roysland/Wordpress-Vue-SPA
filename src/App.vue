<template>
  <div id="app">
    <theme-header></theme-header>
    <main>
      <router-view/>
    </main>
    <theme-footer></theme-footer>
  </div>
</template>

<script>
  import ThemeHeader from './components/theme-header'
  import ThemeFooter from './components/theme-footer'
  export default {
    components: {ThemeHeader, ThemeFooter}
  }
</script>
