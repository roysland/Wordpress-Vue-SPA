<style>

</style>

<template>

<transition name="slide-fade">

  <div class="row rt-main" v-if="loaded ==='true'">

    <div class="medium-12 small-12 column" >

      <div class="rt-post">

        <h2 class="rt-post-title"> {{ page.title.rendered }}</h2>

        <div class="rt-post-content rt-content" v-html="page.content.rendered"></div>


      </div>

    </div>

  </div>

</transition>


</template>

<script>
export default {
  mounted () {
    this.getPage()
  },
  data () {
    return {
      page: {},
      loaded: 'false',
      pageTitle: ''
    }
  },
  methods: {
    getPage () {
      console.log('Fetch page')
    }
  }
}
</script>
