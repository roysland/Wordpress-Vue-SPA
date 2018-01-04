<template>
  <header id="masthead" class="site-header" role="banner">
    <ul>
      <li v-for="item in menus" v-if="item.type != 'custom'">
          <router-link :to="{ name: 'page', params: { name: getUrlName( item.url ) }}"> {{ item.title }} </router-link>
      </li>
    </ul>
  </header>
</template>

<script>
export default {
  mounted () {
    this.getMenu()
  },
  data () {
    return {
      menus: [],
      site_name: 'Theme header',
      isActive: false
    }
  },
  methods: {
    getMenu () {
      console.log('Getting menu')
    }
  }
}
</script>
