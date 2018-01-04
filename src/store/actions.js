import Axios from 'axios'

export default {
  setDefaultTitle ({commit}) {
    Axios.get('http://localhost/wordpress/wp-json/')
    .then((response) => {
      console.log(response.data.description)
    })
    commit('SET_TITLE', 'PageUp Committed')
  }
}
