export default {
  SET_TITLE (state, payload) {
    state.page.title = payload
    document.title = payload
  }
}
