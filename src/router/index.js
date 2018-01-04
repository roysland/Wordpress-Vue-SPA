import Vue from 'vue'
import Router from 'vue-router'

import PageView from '@/components/page'
import BlogView from '@/components/posts'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/blog'
    },
    {
      path: '/blog',
      name: 'BlogView',
      component: BlogView
    },
    {
      path: '/page',
      name: 'PageView',
      component: PageView
    }
  ],
  mode: 'history'
})
