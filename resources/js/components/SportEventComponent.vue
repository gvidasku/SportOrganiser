<template>
  <div class="sportevent-component">
    <div class="row">
      <div class="col-sm-12 col-md-5 col-xl-4">
        <Sidebar
          @get-by-city="getBycity"
          @get-by-sportevent-level="getBysporteventLevel"
          @get-by-sportcategoryType="getBysportcategoryType"
          @get-by-sporttype="getBysporttype"
        />
      </div>
      <div class="col-sm-12 col-md-7 col-xl-8">
        <div v-if="posts.data.length < 1">
          <p class="card-header">Nieko nerasta</p>
          <div class="card-body bg-white text-center">
            <div class="card-text">
              <img
                src="images/nofound3.jpg"
                alt="search-not-found-clip"
              />
              <h4>
                Nieko nerasta <br />
                <span class="text-muted font-size-12px"
                  >Ieškokite kito sporto užsiėmimo.</span
                >
              </h4>
            </div>
          </div>
        </div>
        <div class="card" v-else>
          <SearchResult
            :posts="posts.data"
            :from="posts.from"
            :to="posts.to"
            :total="posts.total"
          />

          <div class="my-4 text-center small">
            <div class="d-block py-2 text-muted">
             Pagal jūsų paiešką rasta: {{ posts.total }} užsiėmimas/užsiėmimai
            </div>
            <div class="d-flex justify-content-center">
              <pagination
                class="custom-pagination"
                :data="posts"
                @pagination-change-page="getsportevents"
              ></pagination>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from "./Sidebar";
import SearchResult from "./SearchResult";

export default {
  name: "sportevent-component",
  components: {
    Sidebar,
    SearchResult,
  },
  data() {
    return {
      posts: [],
    };
  },
  created() {
    this.getsportevents();
  },
  methods: {
    getsportevents(page = 1) {
      this.$Progress.start();
      const query = this.getParameterByName("q", window.location.href);
      const city = this.getParameterByName(
        "city_id",
        window.location.href
      );
      if (query !== "" && query !== null) {
        axios
          .get("/api/search/?q=" + query)
          .then((res) => res.data)
          .then((data) => {
            this.posts = data;
            this.$Progress.finish();
          })
          .catch((err) => {
            console.log(err.message);
            this.$Progress.fail();
          });
      } else if (city !== "" && city !== null) {
        this.getBycity(city);
      } else {
        axios
          .get(`/api/search?page=${page}`)
          .then((res) => res.data)
          .then((data) => {
            this.posts = data;
            console.log(data.data);
            this.$Progress.finish();
          })
          .catch((err) => {
            console.log(err.message);
            this.$Progress.fail();
          });
      }
    },
    getBycity(cityId) {
      this.$Progress.start();
      axios
        .get(`/api/search?city_id=${cityId}`)
        .then((res) => res.data)
        .then((data) => {
          this.posts = data;
          this.$Progress.finish();
        })
        .catch((err) => {
          console.log(err.message);
          this.posts = [];
          this.$Progress.fail();
        });
    },
    getBysporttype(sporttypeLevel) {
      this.$Progress.start();
      axios
        .get(`/api/search?level=${sporttypeLevel}`)
        .then((res) => res.data)
        .then((data) => {
          this.posts = data;
          this.$Progress.finish();
        })
        .catch((err) => {
          console.log(err.message);
          this.posts = [];
          this.$Progress.fail();
        });
    },
    getBysporteventLevel(sporteventLevel) {
      this.$Progress.start();
      axios
        .get(`/api/search?sport_category=${sporteventLevel}`)
        .then((res) => res.data)
        .then((data) => {
          this.posts = data;
          this.$Progress.finish();
        })
        .catch((err) => {
          console.log(err.message);
          this.posts = [];
          this.$Progress.fail();
        });
    },
    getBysportcategoryType(sportcategoryType) {
      this.$Progress.start();
      axios
        .get(`/api/search?event_type=${sportcategoryType}`)
        .then((res) => res.data)
        .then((data) => {
          this.posts = data;
          this.$Progress.finish();
        })
        .catch((err) => {
          console.log(err.message);
          this.posts = [];
          this.$Progress.fail();
        });
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

<style lang="scss" scoped>
</style>



