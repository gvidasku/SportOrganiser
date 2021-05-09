<template>
  <section class="search-bar mt-2 px-0">
    <div class="py-4">
      <div class="row">
        <div class="col-md-6 offset-md-3">
          <form>
            <div class="row m-1">
              <div class="col-md-12 input-group">
                <input
                  type="text"
                  name="q"
                  class="form-control"
                  placeholder="Ieškoti..."
                  v-model="sporteventTitle"
                />
                <span class="input-group-append">
                  <button class="btn btn-success pt-1" @click="searchByTitle">
                    <span class="icon-search"></span> Ieškoti
                  </button>
                </span>
              </div>
            </div>
          </form>
        </div>
        <div class="col-sm-12 col-md-6 offset-md-3 small text-center my-2">
          <div class="row">
            <div class="col-sm-6 col-md-3">
              <router-link to="/">Visi užsiėmimai</router-link>
            </div>
            <div class="col-sm-6 col-md-3">
              <router-link to="/sportevents-by-organization"
                >Pagal Organizatorių</router-link
              >
            </div>
            <div class="col-sm-6 col-md-3">
              <router-link to="/sportevents-by-city">Pagal miestą</router-link>
            </div>
            <div class="col-sm-6 col-md-3">
              <router-link to="/sportevents-by-title">Pagal pavadinimą</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "search-bar",
  data() {
    return {
      sporteventTitle: null,
    };
  },
  mounted() {
    const q = this.getParameterByName("q", window.location.href);
    if (q !== "") {
      this.sporteventTitle = q;
    }
  },
  methods: {
    searchByTitle() {
      if (this.sporteventTitle.trim() != "") {
        this.$emit("searchByTitle", this.sporteventTitle);
      }
    },
    getParameterByName(name, url) {
      if (!url) url = window.location.href;
      name = name.replace(/[\[\]]/g, "\\$&");
      var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
      if (!results) return null;
      if (!results[2]) return "";
      return decodeURIComponent(results[2].replace(/\+/g, " "));
    },
  },
};
</script>

<style scoped>
.search-bar {
  background-color: #f5fdff;
}
</style>>
